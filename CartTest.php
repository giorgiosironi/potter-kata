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
}

class Cart
{
    private $books = 0;
    const PRICE_SINGLE = 8;

    public function addBooks($number)
    {
        $this->books += $number;
    }    

    public function price()
    {
        return self::PRICE_SINGLE * $this->books;
    }
}
