<?php

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
                return $this;
            }
        }
        $bundles = $this->bundles;
        $remainingBooksList = $this->remainingBooksList;
        $bundles[] = $bundle;
        $remainingBooksList[] = $remainingBooks;
        return new self($bundles, $remainingBooksList);
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

    public function asPotentialSolutions()
    {
        $entries = [];
        foreach ($this as $tuple) {
            list($bundle, $remainingBooks) = $tuple;
            $entries[] = PotentialSolution::flyweight([$bundle], $remainingBooks);
        }
        return new PotentialSolutionSet($entries);
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
