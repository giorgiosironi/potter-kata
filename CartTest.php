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

    public function test6BooksInTheCartButThreeAndTwoAreIdentical()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C');
        $this->assertEquals(
            8 * 3 * 0.90 + 
            8 * 2 * 0.95 + 
            8, 
            $this->cart->price()
        );
    }

    public function testMegaAcceptanceTestFromC2Wiki()
    {
        $this->cart->addBooks('A', 2);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C', 2);
        $this->cart->addBooks('D');
        $this->cart->addBooks('E');
        $this->assertEquals(
            4 * 8 * 0.80 + 
            4 * 8 * 0.80,
            $this->cart->price()
        );
    }

    public function testsFromGogoGnome1()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 3);
        $this->cart->addBooks('C', 3);
        $this->cart->addBooks('D', 2);
        $this->cart->addBooks('E', 2);
        $this->assertEquals(
            81.2,
            $this->cart->price()
        );
    }

    public function testsFromGogoGnome2()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C', 4);
        $this->cart->addBooks('D', 2);
        $this->cart->addBooks('E', 1);
        $this->assertEquals(
            78.8,
            $this->cart->price()
        );
    }

    public function testsFromGogoGnome3()
    {
        $this->cart->addBooks('A', 1);
        $this->cart->addBooks('B', 2);
        $this->cart->addBooks('C', 3);
        $this->cart->addBooks('D', 4);
        $this->cart->addBooks('E', 5);
        $this->assertEquals(
            100.0,
            $this->cart->price()
        );
    }

    public function testsFromGogoGnome4()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 4);
        $this->cart->addBooks('C', 3);
        $this->cart->addBooks('D', 6);
        $this->cart->addBooks('E', 6);
        $this->assertEquals(
            141.6,
            $this->cart->price()
        );
    }

    public function testsFromGogoGnome5()
    {
        $this->cart->addBooks('A', 3);
        $this->cart->addBooks('B', 4);
        $this->cart->addBooks('C', 3);
        $this->cart->addBooks('D', 6);
        $this->assertEquals(
            108.0,
            $this->cart->price()
        );
    }

    public function testAnotherMegaAcceptanceTestFromC2Wiki()
    {
        $this->markTestIncomplete("Explodes in computational complexity");
        $this->cart->addBooks('A', 5);
        $this->cart->addBooks('B', 5);
        $this->cart->addBooks('C', 4);
        $this->cart->addBooks('D', 5);
        $this->cart->addBooks('E', 4);
        $this->assertEquals(
            3 * (8 * 5 * 0.75) + 
            2 * (8 * 4 * 0.8), 
            $this->cart->price()
        );
    }
}
