<?php

/**
 * Represent the set of books to buy.
 * It is divided into Bundles to calculate the total price,
 * as the sum of the price of each bundle.
 * This is the only stateful object, the others are immutable.
 */
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

    /**
     * Calculates the optimal price according to possible combinations of book
     * into Bundles. Produces first a greedy solution to prune part of the solution
     * space containing only solutions whose minimum price would be above the 
     * greedy one.
     */
    public function price()
    {
        $greedySolution = $this->greedyBundles();
        $optimalSolution = $this->optimalBundles($greedySolution);
        return $optimalSolution->price();
    }

    private function optimalBundles($greedySolution = null)
    {
        $potentialSolutions = Bundle::extractAllUpTo($this->books, 5)->asPotentialSolutions();

        $finished = false;
        while (!$finished) {
            $finished = true;
            $newSolutionSet = new PotentialSolutionSet();
            foreach ($potentialSolutions as $potentialSolution) {
                if ($greedySolution) {
                    if ($potentialSolution->minimumPrice() > $greedySolution->price()) {
                        continue;
                    }
                }
                if ($potentialSolution->hasRemainingBooks()) {
                    $finished = false;
                    foreach ($potentialSolution->expand(5) as $newBranch) {
                        $newSolutionSet = $newSolutionSet->add($newBranch->anonymous());
                    }
                } else {
                    $newSolutionSet = $newSolutionSet->add($potentialSolution->anonymous());
                }
            }
            $potentialSolutions = $newSolutionSet;

            $max = 0;
            foreach ($potentialSolutions as $potentialSolution) {
                $candidate = array_sum(array_values($potentialSolution->remainingBooks));
                if ($candidate > $max) {
                    $max = $candidate;
                }
            }
            error_log("PotentialSolutionSet now contains " . count($potentialSolutions) . " potential solutions");
            error_log("Maximum remaining books is $max");
        }
        
        return $potentialSolutions->bestSolution();
    }

    private function greedyBundles()
    {
        return (new PotentialSolution([], $this->books))->becomeGreedy();
        $bundles = [];
        $remainingBooks = $this->books;
        while ($remainingBooks) {
            list ($bundle, $remainingBooks) = Bundle::extractGreedily($remainingBooks);
            $bundles[] = $bundle;
        }
    }
}
