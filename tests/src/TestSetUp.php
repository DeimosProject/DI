<?php

namespace DeimosTest;

class TestSetUp extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DI
     */
    protected $di;

    public function setUp()
    {
        $this->di = new DI();
    }

}