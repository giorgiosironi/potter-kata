<?php

class BundleTest extends PHPUnit_Framework_TestCase
{
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
        $this->assertEquals(Bundle::flyweight(['A']), $bundle);
        $this->assertEquals(['B' => 1, 'C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[1];
        $this->assertEquals(Bundle::flyweight(['B']), $bundle);
        $this->assertEquals(['A' => 1, 'C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[2];
        $this->assertEquals(Bundle::flyweight(['C']), $bundle);
        $this->assertEquals(['A' => 1, 'B' => 1], $remainingBooks);
    }

    public function testAllPossibleBundlesOfTwoBooksCanBeRequested()
    {
        $all = Bundle::extractAll(['A' => 1, 'B' => 1, 'C' => 1], 2);

        list ($bundle, $remainingBooks) = $all[0];
        $this->assertEquals(Bundle::flyweight(['A', 'B']), $bundle);
        $this->assertEquals(['C' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[1];
        $this->assertEquals(Bundle::flyweight(['A', 'C']), $bundle);
        $this->assertEquals(['B' => 1], $remainingBooks);

        list ($bundle, $remainingBooks) = $all[2];
        $this->assertEquals(Bundle::flyweight(['B', 'C']), $bundle);
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
        $this->assertEquals(Bundle::flyweight(['A', 'B']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);
    }

    public function testABundleCanBeAnonymizedSinceItsPriceOnlyDependsOnTheNumbersOfBooksContained()
    {
        $this->assertEquals(
            Bundle::flyweight(['A', 'B'])->anonymize(),
            Bundle::flyweight(['C', 'D'])->anonymize()
        );
    }
}
