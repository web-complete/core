<?php

namespace WebComplete\core\utils\helpers;

class StringHelperTest extends \CoreTestCase
{

    public function testStr2Url()
    {
        $helper = new StringHelper();
        $this->assertEquals('proverka-translita-123', $helper->str2url('Проверка транслита 123 '));
    }
}