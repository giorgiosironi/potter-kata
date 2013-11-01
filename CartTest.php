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

    public function testABundleCalculateAPriceOnlyOnDifferentBooks()
    {
        list ($bundle, $remainingBooks) = Bundle::extractFrom(['A' => 1, 'B' => 1]);
        $this->assertEquals(8 * 2 * 0.95, $bundle->price());
    }

    public function testABundleCalculateAPriceOnAnyNumberOfDifferentBooks()
    {
        list ($bundle, $remainingBooks) = Bundle::extractFrom(['A' => 1, 'B' => 1, 'C' => 1, 'D' => 1, 'E' => 1]);
        $this->assertEquals(8 * 5 * 0.75, $bundle->price());
    }

    public function testABundleCanBeExtractedFromAGroupContainingTwoIdenticalBook()
    {
        list ($bundle, $remainingBooks) = Bundle::extractFrom(['A' => 2, 'B' => 1]);
        $this->assertEquals(new Bundle(['A', 'B']), $bundle);
        $this->assertEquals(['A' => 1], $remainingBooks);
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
        list ($bundle, $remainingBooks) = Bundle::extractFrom($this->books);
        $price = $bundle->price();
        if ($remainingBooks) {
            list ($bundle2, ) = Bundle::extractFrom($remainingBooks);
            $price += $bundle2->price();
        }
        return $price;
    }
}

class Bundle
{
    const PRICE_SINGLE = 8;
    private $discountScale = [
        2 => 0.05,
        3 => 0.10,
        4 => 0.20,
        5 => 0.25,
    ];
    private $titles;

    public static function extractFrom(array $books)
    {
        $titles = [];
        foreach ($books as $title => $number) {
            $books[$title]--;
            if ($books[$title] == 0) {
                unset($books[$title]);
            }
            $titles[] = $title;
        }
        return [new self($titles), $books];
    }

    public function __construct($titles)
    {
        $this->titles = $titles;
    }

    public function price()
    {
        $numberOfDifferentBooks = count($this->titles);
        $discount = isset($this->discountScale[$numberOfDifferentBooks]) ? $this->discountScale[$numberOfDifferentBooks] : 0;
        return self::PRICE_SINGLE 
            * $numberOfDifferentBooks
            * (1 - $discount);
    }
}
