<?php

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
        $bundleBags = $this->bundleBags;
        $bundleBags[] = $bag;
        return new self($bundleBags);
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
