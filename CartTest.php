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
        $this->cart->addBooks(1);
        $this->assertEquals(8, $this->cart->price());
    }

    public function test2DifferentBooksInTheCart()
    {
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->assertEquals(8 * 2 * 0.95, $this->cart->price());
    }

    public function test3DifferentBooksInTheCart()
    {
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->assertEquals(8 * 3 * 0.90, $this->cart->price());
    }

    public function test4DifferentBooksInTheCart()
    {
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->cart->addBooks(1);
        $this->assertEquals(8 * 4 * 0.80, $this->cart->price());
    }
}

class Cart
{
    private $books = 0;
    const PRICE_SINGLE = 8;
    private $discountScale = [
        2 => 0.05,
        3 => 0.10,
        4 => 0.20,
    ];

    public function addBooks($number)
    {
        $this->books += $number;
    }    

    public function price()
    {
        $numberOfDifferentBooks = $this->books;
        $discount = isset($this->discountScale[$numberOfDifferentBooks]) ? $this->discountScale[$numberOfDifferentBooks] : 0;
        return self::PRICE_SINGLE 
            * $this->books
            * (1 - $discount);
    }
}
