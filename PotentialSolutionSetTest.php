<?php

class PotentialSolutionSetTest extends PHPUnit_Framework_TestCase
{
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
