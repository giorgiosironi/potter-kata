<?php

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testNoBooksInTheCart()
    {
        $cart = new Cart();
        $this->assertEquals(0, $cart->price());
    }
}

class Cart
{
    public function price()
    {
        return 0;
    }
}
