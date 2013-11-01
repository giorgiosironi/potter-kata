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

    public function testAnotherMegaAcceptanceTestFromC2Wiki()
    {
        $this->markTestIncomplete("Explodes in computational complexity");
        $this->cart->addBooks('A', 5);
        $this->cart->addBooks('B', 5);
        $this->cart->addBooks('C', 4);
        $this->cart->addBooks('D', 5);
        $this->cart->addBooks('E', 4);
        $this->assertEquals(
            3 * (8 * 5 * 0.75) + 
            2 * (8 * 4 * 0.8), 
            $this->cart->price()
        );
    }

    // -- Bundle unit tests

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

    public function testAllPossibleBundlesOfTwoBooksCanBeRequested()
    {
        $all = Bundle::extractAll(['A' => 1, 'B' => 1, 'C' => 1], 2);

        list ($bundle, $remainingBooks) = $all[0];
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[1];
        $this->assertEquals(new Bundle(['A', 'C']), $bundle);
        $this->assertEquals(['B' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[2];
        $this->assertEquals(new Bundle(['B', 'C']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);
    }

    public function testAllPossibleBundlesOfUpToNBooksCanBeRequested()
    {
        $all = Bundle::extractAllUpTo(['A' => 1, 'B' => 1, 'C' => 1], 2);
        $this->assertEquals(6, count($all));
    }

    public function testIdenticalBooksCannotGoInTheSameBundle()
    {
        $all = Bundle::extractAll(['A' => 2, 'B' => 1], 2);
        $this->assertEquals(1, count($all));

        list ($bundle, $remainingBooks) = $all[0];
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);
    }

    public function testABundleCanBeAnonymizedSinceItsPriceOnlyDependsOnTheNumbersOfBooksContained()
    {
        $this->assertEquals(
            (new Bundle(['A', 'B']))->anonymize(),
            (new Bundle(['C', 'D']))->anonymize()
        );
    }

    // BundleBag test
    public function testABundleBagCanGiveAMinimumEstimateOfItsPriceByConsideringOnlyTheBundlesAndNotTheRemainingBooks()
    {
        $bundleBag = BundleBag::fromString('A;B|C=1,D=1');
        $this->assertEquals(8 * 2, $bundleBag->minimumPrice());
    }

    public function testAnAnonymousBundleBagChangesTheNameOfTheBooksToCanonicalOnesToBeEqualToEuivalentOnes()
    {
        $this->assertEquals(
            BundleBag::fromString('A;B|C=2;D=1')->anonymous(),
            BundleBag::fromString('A;B|C=1;D=2')->anonymous()
        );
    }

    // BundleBagSet tests
    public function testBundleBagSetsCannotContainDuplicateBundleBags()
    {
        $this->assertEquals(
            new BundleBagSet([BundleBag::fromString('A,B|')]),
            (new BundleBagSet([BundleBag::fromString('A,B|')]))->add(BundleBag::fromString('A,B|'))
        );
    }

    public function testBundleBagsCreatedInDifferentOrderAreStillIdentical()
    {
        $this->assertEquals(
            new BundleBagSet([BundleBag::fromString('A;B|')]),
            (new BundleBagSet([BundleBag::fromString('B;A|')]))->add(BundleBag::fromString('A;B|'))
        );
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
                $bags->add($bag->anonymous());
            }
            foreach ($bags as $bag) {
                error_log($bag);
            }
            error_log("BundleBagSet now contains " . count($bags) . " bags");
        }
        
        return $bags->minimumBag();
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
                [new self([]), $books]
            ];
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
                $all->add(
                    $newBundle,
                    $remainingBooks
                );
            }
        }
        return $all;
    }

    public function __construct($titles)
    {
        sort($titles);
        $this->titles = $titles;
    }

    public function __toString()
    {
        return implode(',', $this->titles);
    }

    public static function fromString($representation)
    {
        return new self(explode(',', $representation));
    }

    public function price()
    {
        $numberOfDifferentBooks = count($this->titles);
        $discount = $this->discountScale[$numberOfDifferentBooks];
        return self::PRICE_SINGLE 
            * $numberOfDifferentBooks
            * (1 - $discount);
    }

    public function anonymize()
    {
        $titles = array_map(function() { return 'X'; }, $this->titles);
        return new self($titles);
    }

    public function merge($title)
    {
        return new self(array_merge($this->titles, [$title]));
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

class BundleSet implements IteratorAggregate, ArrayAccess, Countable
{
    public function __construct($bundles = [], $remainingBooksList = [])
    {
        $this->bundles = $bundles;
        $this->remainingBooksList = $remainingBooksList;
    }

    public function add(Bundle $bundle, array $remainingBooks)
    {
        foreach ($this->bundles as $each) {
            if ($each == $bundle) {
                return;
            }
        }
        $this->bundles[] = $bundle;
        $this->remainingBooksList[] = $remainingBooks;
    }

    public function getIterator()
    {
        $entries = [];
        foreach ($this->bundles as $key => $bundle) {
            $remainingBooks = $this->remainingBooksList[$key];
            $entries[] = [$bundle, $remainingBooks];
        }
        return new ArrayIterator($entries);
    }

    public function asBags()
    {
        $entries = [];
        foreach ($this as $tuple) {
            list($bundle, $remainingBooks) = $tuple;
            $entries[] = new BundleBag([$bundle], $remainingBooks);
        }
        return new BundleBagSet($entries);
    }

    public function count()
    {
        return count($this->bundles);
    }

    /**
     * Does not guarantee no duplicates
     */
    public function merge(BundleSet $another)
    {
        return new self(
            array_merge($this->bundles, $another->bundles),
            array_merge($this->remainingBooksList, $another->remainingBooksList)
        );
    }

    public function anonymous()
    {
        $bundles = array_map(function(Bundle $bundle) { return $bundle->anonymize(); }, $this->bundles);
        return new self($bundles, $this->remainingBooksList);
    }

    public function offsetGet($offset)
    {
        return [$this->bundles[$offset], $this->remainingBooksList[$offset]];
    }

    public function offsetExists($offset)
    {
        throw new Exception();
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception();
    }

    public function offsetUnset($offset)
    {
        throw new Exception();
    }
}

class BundleBag
{
    public function __construct(array $bundles, array $remainingBooks)
    {
        usort($bundles, function($bundleA, $bundleB) {
            return strcmp((string) $bundleA, (string) $bundleB);
        });
        $this->bundles = $bundles;
        $this->remainingBooks = $remainingBooks;
    }

    public function hasRemainingBooks()
    {
        return count($this->remainingBooks) > 0;
    }

    public function __toString()
    {
        $remainingBooks = [];
        foreach ($this->remainingBooks as $title => $number) {
            $remainingBooks[] = "{$title}={$number}";
        }
        return implode(';', $this->bundles) . '|' . implode(';', $remainingBooks);
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
        return new self($bags, $remainingBooks);
    }

    public function add(Bundle $bundle, array $newRemainingBooks)
    {
        return new self(
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
        return new self(
            $this->bundles,
            $canonicalRemainingBooks
        );
    }
}

class BundleBagSet implements IteratorAggregate, Countable
{
    public function __construct(array $bundleBags = [])
    {
        $this->bundleBags = $bundleBags;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->bundleBags);
    }

    public function count()
    {
        return count($this->bundleBags);
    }

    public function add(BundleBag $bag)
    {
        foreach ($this->bundleBags as $each) {
            if ($each == $bag) {
                return $this;
            }
        }
        $this->bundleBags[] = $bag;
        return $this;
    }

    public function minimumBag()
    {
        if (!$this->first()) {
            return BundleBag::fromString('|');
        }
        $minimum = $this->first()->price();
        $minimumBag = $this->first();
        foreach ($this->bundleBags as $bag) {
            if ($bag->price() < $minimum) {
                $minimum = $bag->price();
                $minimumBag = $bag;
            }
        }
        echo "Evaluated " . count($this->bundleBags) . " solutions." , PHP_EOL;
        return $minimumBag;
    }

    private function first()
    {
        reset($this->bundleBags);
        return current($this->bundleBags);
    }
}
