<?php

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cart = new Cart();
    }

    public function testNoBooksInTheCart()
    {
        $this->assertEquals(0, $this->cart->price());
    }

    public function test1BookInTheCart()
    {
        $this->cart->addBooks('A');
        $this->assertEquals(8, $this->cart->price());
    }

    public function test2DifferentBooksInTheCart()
    {
        $this->cart->addBooks('A');
        $this->cart->addBooks('B');
        $this->assertEquals(8 * 2 * 0.95, $this->cart->price());
    }

    public function test3DifferentBooksInTheCart()
    {
        $this->cart->addBooks('A');
        $this->cart->addBooks('B');
        $this->cart->addBooks('C');
        $this->assertEquals(8 * 3 * 0.90, $this->cart->price());
    }

    public function test4DifferentBooksInTheCart()
    {
        $this->cart->addBooks('A');
        $this->cart->addBooks('B');
        $this->cart->addBooks('C');
        $this->cart->addBooks('D');
        $this->assertEquals(8 * 4 * 0.80, $this->cart->price());
    }

    public function test5DifferentBooksInTheCart()
    {
        $this->cart->addBooks('A');
        $this->cart->addBooks('B');
        $this->cart->addBooks('C');
        $this->cart->addBooks('D');
        $this->cart->addBooks('E');
        $this->assertEquals(8 * 5 * 0.75, $this->cart->price());
    }

    public function test4BooksInTheCartButTwoAreIdentical()
    {
        $this->cart->addBooks('A', 2);
        $this->cart->addBooks('B');
        $this->cart->addBooks('C');
        $this->assertEquals(8 * 3 * 0.90 + 8, $this->cart->price());
    }

    public function test6BooksInTheCartButThreeAndTwoAreIdentical()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C');
        $this->assertEquals(
            8 * 3 * 0.90 + 
            8 * 2 * 0.95 + 
            8, 
            $this->cart->price()
        );
    }

    public function testMegaAcceptanceTestFromC2Wiki()
    {
        $this->cart->addBooks('A', 2);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C', 2);
        $this->cart->addBooks('D');
        $this->cart->addBooks('E');
        $this->assertEquals(
            4 * 8 * 0.80 + 
            4 * 8 * 0.80,
            $this->cart->price()
        );
    }

    // -- Bundle unit tests

    public function testABundleCalculateAPriceOnlyOnDifferentBooks()
    {
        list ($bundle, $remainingBooks) = Bundle::extractGreedily(['A' => 1, 'B' => 1]);
        $this->assertEquals(8 * 2 * 0.95, $bundle->price());
    }

    public function testABundleCalculateAPriceOnAnyNumberOfDifferentBooks()
    {
        list ($bundle, $remainingBooks) = Bundle::extractGreedily(['A' => 1, 'B' => 1, 'C' => 1, 'D' => 1, 'E' => 1]);
        $this->assertEquals(8 * 5 * 0.75, $bundle->price());
    }

    public function testABundleCanBeExtractedFromAGroupContainingTwoIdenticalBook()
    {
        list ($bundle, $remainingBooks) = Bundle::extractGreedily(['A' => 2, 'B' => 1]);
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);
    }

    public function testAMaximumCardinalityOfABundleCanBeRequested()
    {
        list ($bundle, $remainingBooks) = Bundle::extractGreedily(['A' => 1, 'B' => 1, 'C' => 1], 2);
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['C' => 1], $remainingBooks);
    }

    public function testAllPossibleBundlesOfSingleBooksCanBeRequested()
    {
        $all = Bundle::extractAll(['A' => 1, 'B' => 1, 'C' => 1], 1);

        list ($bundle, $remainingBooks) = $all[0];
        $this->assertEquals(new Bundle(['A']), $bundle);
        $this->assertEquals(['B' => 1, 'C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[1];
        $this->assertEquals(new Bundle(['B']), $bundle);
        $this->assertEquals(['A' => 1, 'C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[2];
        $this->assertEquals(new Bundle(['C']), $bundle);
        $this->assertEquals(['A' => 1, 'B' => 1], $remainingBooks);
    }

    public function testAllPossibleBundlesOfTwoBooksBooksCanBeRequested()
    {
        $all = Bundle::extractAll(['A' => 1, 'B' => 1, 'C' => 1], 2);

        list ($bundle, $remainingBooks) = $all[0];
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[1];
        $this->assertEquals(new Bundle(['B', 'C']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[2];
        $this->assertEquals(new Bundle(['A', 'C']), $bundle);
        $this->assertEquals(['B' => 1], $remainingBooks);
    }

    public function testMovementOfBooksBetweenBundles()
    {
        $targetBundle = new Bundle(['A', 'B']);
        $sourceBundle = new Bundle(['C', 'D']);
        $sourceBundle->move('D', $targetBundle);
        $this->assertEquals(3, count($targetBundle));
        $this->assertEquals(1, count($sourceBundle));
    }

    public function testMovementOfANumberOfBooksBetweenBundlesCanBeRequested()
    {
        $targetBundle = new Bundle(['A', 'B']);
        $sourceBundle = new Bundle(['C', 'D']);
        $sourceBundle->move(1, $targetBundle);
        $this->assertEquals(new Bundle(['A', 'B', 'C']), $targetBundle);
        $this->assertEquals(new Bundle(['D']), $sourceBundle);
    }

    public function testNoDuplicatesCanBeCreatedDuringMovement()
    {
        $targetBundle = new Bundle(['A', 'B']);
        $sourceBundle = new Bundle(['A', 'C']);
        $sourceBundle->move(1, $targetBundle);
        $this->assertEquals(new Bundle(['A', 'B', 'C']), $targetBundle);
        $this->assertEquals(new Bundle(['A']), $sourceBundle);
    }

    public function testPossibleMovements()
    {
        $bundle = new Bundle([]);
        foreach ($bundle->discountScale as $numberA => $discountA) {
            foreach ($bundle->discountScale as $numberB => $discountB) {
                if ($numberA == 1) {
                    continue;
                }
                if ($numberB == 5) {
                    continue;
                }
                $current = $numberA * (1 - $discountA) + $numberB * (1 - $discountB);
                $targetNumberA = $numberA - 1;
                $targetNumberB = $numberB + 1;
                $targetRight = ($targetNumberA) * (1 - $bundle->discountScale[$targetNumberA]) +
                               ($targetNumberB) * (1 - $bundle->discountScale[$targetNumberB]);
                if ($targetRight < $current) {
                    echo "Moving from $numberA, $numberB to $targetNumberA, $targetNumberB gives a better discount (from $current to $targetRight)", PHP_EOL;
                }
            }
        }
    }
}

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
        return $this->sumOf($this->optimize($this->divideInBundles()));
    }

    private function divideInBundles()
    {
        $bundles = [];
        $remainingBooks = $this->books;
        while ($remainingBooks) {
            list ($bundle, $remainingBooks) = Bundle::extractGreedily($remainingBooks);
            $bundles[] = $bundle;
        }
        return $bundles;
    }

    private function optimize(array $bundles)
    {
        $candidateSources = [];
        foreach ($bundles as $bundle) {
            if (count($bundle) == 5) {
                $candidateSources[] = $bundle;
            }
        }
        $candidateTargets = [];
        foreach ($bundles as $bundle) {
            if (count($bundle) == 3) {
                $candidateTargets[] = $bundle;
            }
        }
        for ($i = 0; $i < count($candidateSources) && $i < count($candidateTargets); $i++) {
            $candidateSources[$i]->move(1, $candidateTargets[$i]);
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

class Bundle implements Countable
{
    const PRICE_SINGLE = 8;
    public $discountScale = [
        1 => 0.00,
        2 => 0.05,
        3 => 0.10,
        4 => 0.20,
        5 => 0.25,
    ];
    private $titles;

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

    public static function extractAll(array $books, $cardinality)
    {
        $all = [];
        foreach ($books as $title => $number) {
            $remainingBooks = $books;
            $bundle = new self([$title]);
            $remainingBooks[$title]--;
            if ($remainingBooks[$title] == 0) {
                unset($remainingBooks[$title]);
            }
            $all[] = [
                $bundle,
                $remainingBooks
            ];
        }
        return $all;
    }

    public function __construct($titles)
    {
        $this->titles = $titles;
    }

    public function price()
    {
        $numberOfDifferentBooks = count($this->titles);
        $discount = $this->discountScale[$numberOfDifferentBooks];
        return self::PRICE_SINGLE 
            * $numberOfDifferentBooks
            * (1 - $discount);
    }

    public function move($title, Bundle $target)
    {
        if (is_string($title)) {
            unset($this->titles[array_search($title, $this->titles)]);
            $target->titles[] = $title;
        } else {
            $howMany = $title;
            if ($howMany == 0) {
                return;
            }
            // support only $howMany == 1
            $possibleMovements  = array_diff($this->titles, $target->titles);
            $this->move(reset($possibleMovements), $target);
            $this->move($howMany - 1, $target);
            $this->titles = array_values($this->titles);
            $target->titles = array_values($target->titles);
        }
    }

    public function count()
    {
        return count($this->titles);
    }
}
