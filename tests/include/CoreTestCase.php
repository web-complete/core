<?php

use Mvkasatkin\mocker\Mocker;

class CoreTestCase extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        parent::setUp();
        Mocker::init($this);
    }
}