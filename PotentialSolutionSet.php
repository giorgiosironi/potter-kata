<?php

/**
 * A set of solutions, representing the whole solution space
 * or a pruned part of it.
 */
class PotentialSolutionSet implements IteratorAggregate, Countable
{
    public function __construct(array $potentialSolutions = [])
    {
        $this->potentialSolutions = $potentialSolutions;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->potentialSolutions);
    }

    public function count()
    {
        return count($this->potentialSolutions);
    }

    public function add(PotentialSolution $potentialSolution)
    {
        foreach ($this->potentialSolutions as $each) {
            if ($each == $potentialSolution) {
                return $this;
            }
        }
        $potentialSolutions = $this->potentialSolutions;
        $potentialSolutions[] = $potentialSolution;
        return new self($potentialSolutions);
    }

    public function bestSolution()
    {
        if (!$this->first()) {
            return PotentialSolution::fromString('|');
        }
        $minimum = $this->first()->price();
        $bestSolution = $this->first();
        foreach ($this->potentialSolutions as $potentialSolution) {
            if ($potentialSolution->price() < $minimum) {
                $minimum = $potentialSolution->price();
                $bestSolution = $potentialSolution;
            }
        }
        echo "Evaluated " . count($this->potentialSolutions) . " solutions." , PHP_EOL;
        return $bestSolution;
    }

    private function first()
    {
        reset($this->potentialSolutions);
        return current($this->potentialSolutions);
    }
}
