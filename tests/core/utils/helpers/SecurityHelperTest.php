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

    public function testMaskToken()
    {
        $helper = new SecurityHelper();
        $maskedToken = $helper->maskToken('123');
        $unmaskToken = $helper->unmaskToken($maskedToken);

        $this->assertNotEquals('123', $maskedToken);
        $this->assertEquals('123', $unmaskToken);
    }

    public function testGenerateRandomString()
    {
        $helper = new SecurityHelper();
        $string1 = $helper->generateRandomString(50);
        $string2 = $helper->generateRandomString(50);
        $this->assertEquals(50, strlen($string1));
        $this->assertNotEquals($string1, $string2);
    }
}