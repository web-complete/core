<?php

namespace WebComplete\core\utils\helpers;

use PHPUnit\Framework\TestCase;

class SecurityHelperTest extends TestCase
{

    public function testCryptPassword()
    {
        $helper = new SecurityHelper();
        $expected = \strtoupper(\md5(\md5('bbb') . \md5('aaa')));
        $this->assertEquals($expected, $helper->cryptPassword('aaa', 'bbb'));
    }
}