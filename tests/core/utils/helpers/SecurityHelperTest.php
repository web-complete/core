<?php

namespace WebComplete\core\utils\helpers;

use PHPUnit\Framework\TestCase;

class SecurityHelperTest extends TestCase
{

    public function testCryptPassword()
    {
        $helper = new SecurityHelper(new StringHelper());
        $expected = \strtoupper(\md5(\md5('bbb') . \md5('aaa')));
        $this->assertEquals($expected, $helper->cryptPassword('aaa', 'bbb'));
    }

    public function testMaskToken()
    {
        $helper = new SecurityHelper(new StringHelper());
        $maskedToken = $helper->maskToken('123');
        $unmaskToken = $helper->unmaskToken($maskedToken);

        $this->assertNotEquals('123', $maskedToken);
        $this->assertEquals('123', $unmaskToken);
    }
}