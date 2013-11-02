<?php

/**
 * Represents a set of books different from each other (up to 5)
 * that are clustered together to get a discount (only on the books in the Bundle).
 */
class Bundle implements Countable
{
    const PRICE_SINGLE = 8;
    private static $discountScale = [
        1 => 0.00,
        2 => 0.05,
        3 => 0.10,
        4 => 0.20,
        5 => 0.25,
    ];
    private $titles;
    private $string;
    private static $bundlesMemoization;
    private static $cache = [];

    public static function extractGreedily(array $books, $maximumCardinality = 5)
    {
        $titles = [];
        $extracted = 0;
        foreach ($books as $title => $number) {
            $books[$title]--;
            if ($books[$title] == 0) {
                unset($books[$title]);
            }
            $titles[] = $title;
            $extracted++;
            if ($extracted == $maximumCardinality) {
                break;
            }
        }
        return [new self($titles), $books];
    }

    public static function extractAllUpTo(array $books, $maximumCardinality)
    {
        $bundleSet = new BundleSet();
        for ($i = 1; $i <= $maximumCardinality; $i++) {
            $bundleSet = $bundleSet->merge(self::extractAll($books, $i)->anonymous());
        }
        return $bundleSet;
    }

    public static function extractAll(array $books, $cardinality)
    {
        if ($cardinality == 0) {
            return [
                [self::flyweight([]), $books]
            ];
        }

        $signatureBooks = [];
        foreach ($books as $title => $number) {
            $signatureBooks[] = "{$title}={$number}";
        }
        $signature = implode(";", $signatureBooks) . "|" . $cardinality;

        if (isset(self::$bundlesMemoization[$signature])) {
            return self::$bundlesMemoization[$signature];
        }

        $all = new BundleSet();
        $previousCardinalityBundles = self::extractAll($books, $cardinality - 1);
        foreach ($previousCardinalityBundles as $tuple) {
            list($bundle, $remainingBooksForBundle) = $tuple;
            foreach ($remainingBooksForBundle as $title => $number) {
                $remainingBooks = $remainingBooksForBundle;
                if ($bundle->contains($title)) {
                    continue;
                }
                $newBundle = $bundle->merge($title);
                $remainingBooks[$title]--;
                if ($remainingBooks[$title] == 0) {
                    unset($remainingBooks[$title]);
                }
                $all = $all->add(
                    $newBundle,
                    $remainingBooks
                );
            }
        }
        self::$bundlesMemoization[$signature] = $all;
        return $all;
    }

    public function __construct($titles)
    {
        sort($titles);
        $this->titles = $titles;
    }

    public static function flyweight($titles)
    {
        $string = implode(',', $titles);
        if (!isset(self::$cache[$string])) {
            self::$cache[$string] = $flyweight = new self($titles);
            self::$cache[(string) $flyweight] = $flyweight;
        }
        return self::$cache[$string];
    }

    public function __toString()
    {
        if ($this->string === null) {
           $this->string = implode(',', $this->titles);
        }
        return $this->string;
    }

    public static function fromString($representation)
    {
        return self::flyweight(explode(',', $representation));
    }

    public static function bestPossiblePrice($numberOfBooks)
    {
        $bestDiscount = end(self::$discountScale);
        return self::PRICE_SINGLE 
            * $numberOfBooks
            * (1 - $bestDiscount);
    }

    public function price()
    {
        $numberOfDifferentBooks = count($this->titles);
        $discount = self::$discountScale[$numberOfDifferentBooks];
        return self::PRICE_SINGLE 
            * $numberOfDifferentBooks
            * (1 - $discount);
    }

    public function anonymous()
    {
        $titles = array_map(function() { return 'X'; }, $this->titles);
        return self::flyweight($titles);
    }

    public function merge($title)
    {
        return self::flyweight(array_merge($this->titles, [$title]));
    }

    public function contains($title)
    {
        return array_search($title, $this->titles) !== false;
    }

    public function count()
    {
        return count($this->titles);
    }
}
