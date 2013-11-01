<?php

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testNoBooksInTheCart()
    {
        $cart = new Cart();
        $this->assertEquals(0, $cart->price());
    }

    public function test1BookInTheCart()
    {
        $cart = new Cart();
        $cart->addBooks(1);
        $this->assertEquals(8, $cart->price());
    }
}

class Cart
{
    private $books = 0;

    public function addBooks($number)
    {
        $this->books += $number;
    }    

    public function price()
    {
        return 8 * $this->books;
    }
}
