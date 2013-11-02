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
        $greedySolution = $this->greedyBundles();
        $optimalSolution = $this->optimalBundles($greedySolution);
        var_dump($greedySolution->price(), $optimalSolution->price());
        return $optimalSolution->price();
    }

    private function optimalBundles($greedySolution = null)
    {
        error_log("Optimal bundles");
        $potentialSolutions = Bundle::extractAllUpTo($this->books, 5)->asPotentialSolutions();

        $finished = false;
        while (!$finished) {
            $finished = true;
            $newSolutionSet = [];
            foreach ($potentialSolutions as $potentialSolution) {
                if ($greedySolution) {
                    if ($potentialSolution->minimumPrice() > $greedySolution->price()) {
                        continue;
                    }
                }
                if ($potentialSolution->hasRemainingBooks()) {
                    $finished = false;
                    $newSolutionSet = array_merge($newSolutionSet, $potentialSolution->expand(5));
                } else {
                    $newSolutionSet = array_merge($newSolutionSet, [$potentialSolution]);
                }
            }
            $potentialSolutions = new PotentialSolutionSet();
            foreach ($newSolutionSet as $potentialSolution) {
                $potentialSolutions = $potentialSolutions->add($potentialSolution->anonymous());
            }

            $max = 0;
            foreach ($potentialSolutions as $potentialSolution) {
                $candidate = array_sum(array_values($potentialSolution->remainingBooks));
                if ($candidate > $max) {
                    $max = $candidate;
                }
                //error_log($potentialSolution);
            }
            error_log("PotentialSolutionSet now contains " . count($potentialSolutions) . " potential solutions");
            error_log("Maximum remaining books is $max");
        }
        
        return $potentialSolutions->bestSolution();
    }

    private function greedyBundles()
    {
        return (new PotentialSolution([], $this->books))->becomeGreedy();
        $bundles = [];
        $remainingBooks = $this->books;
        while ($remainingBooks) {
            list ($bundle, $remainingBooks) = Bundle::extractGreedily($remainingBooks);
            $bundles[] = $bundle;
        }
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
