<?php

namespace WebComplete\core\utils\helpers;

class StringHelperTest extends \CoreTestCase
{

    public function testStr2Url()
    {
        $helper = new StringHelper();
        $this->assertEquals('proverka-translita-123', $helper->str2url('Проверка транслита 123 '));
    }

    public function testHtml2text()
    {
        $helper = new StringHelper();
        $this->assertEquals(
            "Заголовок\n Текст1 Текст2\nТекст3",
            $helper->html2text("<h1>Заголовок</h1>\n<div>Текст1\nТекст2</div>Текст3")
        );
    }
}
