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
       // $this->markTestIncomplete("Explodes in computational complexity");
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

    // PotentialSolution test
    public function testAPotentialSolutionCanGiveAMinimumEstimateOfItsPriceByConsideringOnlyTheBundlesAndNotTheRemainingBooks()
    {
        $bundleBag = PotentialSolution::fromString('A;B|C=1,D=1');
        $this->assertEquals(8 * 2, $bundleBag->minimumPrice());
    }

    public function testAnAnonymousPotentialSolutionChangesTheNameOfTheBooksToCanonicalOnesToBeEqualToEquivalentOnes()
    {
        $this->assertEquals(
            PotentialSolution::fromString('A;B|C=2;D=1')->anonymous(),
            PotentialSolution::fromString('A;B|C=1;D=2')->anonymous()
        );
    }

    public function testPotentialSolutionAlsoReordersBundlesWhenTheBagIsEquivalent()
    {
        $this->assertEquals(
            PotentialSolution::fromString('X;X,X|C=1'),
            PotentialSolution::fromString('X,X;X|C=1')
        );
    }

    // PotentialSolutionSet tests
    public function testPotentialSolutionSetsCannotContainDuplicatePotentialSolutions()
    {
        $this->assertEquals(
            new PotentialSolutionSet([PotentialSolution::fromString('A,B|')]),
            (new PotentialSolutionSet([PotentialSolution::fromString('A,B|')]))->add(PotentialSolution::fromString('A,B|'))
        );
    }

    public function testPotentialSolutionsCreatedInDifferentOrderAreStillIdentical()
    {
        $this->assertEquals(
            new PotentialSolutionSet([PotentialSolution::fromString('A;B|')]),
            (new PotentialSolutionSet([PotentialSolution::fromString('B;A|')]))->add(PotentialSolution::fromString('A;B|'))
        );
    }
}
