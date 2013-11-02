<?php

class Cart
{
    private $books = [];

    public function addBooks($title, $number = 1)
    {
        if (!isset($this->books[$title])) {
            $this->books[$title] = 0;
        }
        $this->books[$title] += $number;
    }    

    public function price()
    {
        return $this->optimalBundles()->price();
    }

    private function optimalBundles()
    {
        error_log("Optimal bundles");
        $bags = Bundle::extractAllUpTo($this->books, 5)->asBags();

        $finished = false;
        while (!$finished) {
            $finished = true;
            $heightTwo = [];
            foreach ($bags as $bag) {
                if ($bag->hasRemainingBooks()) {
                    $finished = false;
                    $heightTwo = array_merge($heightTwo, $bag->expand(5));
                } else {
                    $heightTwo = array_merge($heightTwo, [$bag]);
                }
            }
            $bags = new BundleBagSet();
            foreach ($heightTwo as $bag) {
                $bags = $bags->add($bag->anonymous());
            }
            $max = 0;
            foreach ($bags as $bag) {
                $candidate = array_sum(array_values($bag->remainingBooks));
                if ($candidate > $max) {
                    $max = $candidate;
                }
                //error_log($bag);
            }
            error_log("BundleBagSet now contains " . count($bags) . " bags");
            error_log("Maximum remaining books is $max");
        }
        
        return $bags->minimumBag();
    }

    private function greedyBundles()
    {
        $bundles = [];
        $remainingBooks = $this->books;
        while ($remainingBooks) {
            list ($bundle, $remainingBooks) = Bundle::extractGreedily($remainingBooks);
            $bundles[] = $bundle;
        }
        return $bundles;
    }

    private function sumOf(array $bundles)
    {
        $price = 0;
        foreach ($bundles as $bundle) {
            $price += $bundle->price();
        }
        return $price;
    }
}
