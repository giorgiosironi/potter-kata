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
}

class Cart
{
    private $books = 0;
    const PRICE_SINGLE = 8;
    const DISCOUNT_SINGLE = 0.05;

    public function addBooks($number)
    {
        $this->books += $number;
    }    

    public function price()
    {
        $discount = ($this->books - 1) * self::DISCOUNT_SINGLE;
        return self::PRICE_SINGLE 
            * $this->books
            * (1 - $discount);
    }
}
