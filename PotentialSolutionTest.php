<?php

class PotentialSolutionTest extends PHPUnit_Framework_TestCase
{
    public function testAPotentialSolutionCanGiveAMinimumEstimateOfItsPriceByConsideringOnlyTheBundlesAndNotTheRemainingBooks()
    {
        $potentialSolution = PotentialSolution::fromString('A;B|C=1;D=1');
        $this->assertGreaterThanOrEqual(8 * 2, $potentialSolution->minimumPrice());
    }

    public function testAPotentialSolutionMinimumPriceCanAlsoConsideringTheRemainingBooksAtTheBestPossibleDiscount()
    {
        $potentialSolution = PotentialSolution::fromString('A;B|C=1;D=1');
        $this->assertEquals(8 * 2 + 8 * 2 * 0.75, $potentialSolution->minimumPrice());
    }

    public function testAnAnonymousPotentialSolutionChangesTheNameOfTheBooksToCanonicalOnesToBeEqualToEquivalentOnes()
    {
        $this->assertEquals(
            PotentialSolution::fromString('A;B|C=2;D=1')->anonymous(),
            PotentialSolution::fromString('A;B|C=1;D=2')->anonymous()
        );
    }

    public function testPotentialSolutionAlsoReordersBundlesWhenTheirSetIsEqual()
    {
        $this->assertEquals(
            PotentialSolution::fromString('X;X,X|C=1'),
            PotentialSolution::fromString('X,X;X|C=1')
        );
    }
}
