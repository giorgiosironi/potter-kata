<?php

class PotentialSolution
{
    private $string;
    private static $cache;

    public function __construct(array $bundles, array $remainingBooks)
    {
        usort($bundles, function($bundleA, $bundleB) {
            return strcmp((string) $bundleA, (string) $bundleB);
        });
        $this->bundles = $bundles;
        $this->remainingBooks = $remainingBooks;
    }

    public static function flyweight(array $bundles, array $remainingBooks)
    {
        $remainingBooksStr = [];
        foreach ($remainingBooks as $title => $number) {
            $remainingBooksStr[] = "{$title}={$number}";
        }
        $signature = implode(';', $bundles) . '|' . implode(';', $remainingBooksStr);
        if (!isset(self::$cache[$signature])) {
            self::$cache[$signature] = $flyweight = new self($bundles, $remainingBooks);
            self::$cache[(string) $signature] = $flyweight;
        }
        return self::$cache[$signature];
    }

    public function hasRemainingBooks()
    {
        return count($this->remainingBooks) > 0;
    }

    public function __toString()
    {
        if ($this->string === null) {
            $remainingBooks = [];
            foreach ($this->remainingBooks as $title => $number) {
                $remainingBooks[] = "{$title}={$number}";
            }
            $this->string = implode(';', $this->bundles) . '|' . implode(';', $remainingBooks);
        }
        return $this->string;
    }

    public static function fromString($representation)
    {
        list ($bagsRepresentations, $remainingBooksRepresentation) = explode("|", $representation);
        $bags = [];
        if ($bagsRepresentations) {
            foreach (explode(";", $bagsRepresentations) as $bagRepresentation) {
                $bags[] = Bundle::fromString($bagRepresentation); 
            }
        }
        $remainingBooks = [];
        if ($remainingBooksRepresentation) {
            foreach (explode(";", $remainingBooksRepresentation) as $remainingBook) {
                list ($title, $number) = explode('=', $remainingBook);
                $remainingBooks[$title] = $number;
            }
        }
        return self::flyweight($bags, $remainingBooks);
    }

    public function add(Bundle $bundle, array $newRemainingBooks)
    {
        return self::flyweight(
            array_merge($this->bundles, [$bundle]),
            $newRemainingBooks
        );
    }
    
    public function minimumPrice()
    {
        $minimum = 0;
        foreach ($this->bundles as $bundle) {
            $minimum += $bundle->price();
        }
        return $minimum;
    }

    public function price()
    {
        if ($this->remainingBooks) {
            throw new Exception("There are still books to assign to Bundles, I don't know my final price");
        }
        $price = 0;
        foreach ($this->bundles as $bundle) {
            $price += $bundle->price();
        }
        return $price;
    }

    public function expand($bundleMaximumCardinality)
    {
        $heightTwo = [];
        foreach (Bundle::extractAllUpTo($this->remainingBooks, $bundleMaximumCardinality) as $secondTuple) {
            list($bundle, $newRemainingBooks) = $secondTuple;
            $heightTwo[] = $this->add($bundle, $newRemainingBooks);
        }
        return $heightTwo;
    }

    public function anonymous()
    {
        $copiesOfEachBook = array_values($this->remainingBooks);
        rsort($copiesOfEachBook);
        $canonicalTitles = ['A', 'B', 'C', 'D', 'E'];
        $canonicalRemainingBooks = [];
        foreach ($copiesOfEachBook as $index => $value) {
            $canonicalRemainingBooks[$canonicalTitles[$index]] = $value;
        }
        return self::flyweight(
            $this->bundles,
            $canonicalRemainingBooks
        );
    }
}
