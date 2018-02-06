<?php

namespace WebComplete\core\utils\helpers;

class StringHelperTest extends \CoreTestCase
{

    public function testStr2Url()
    {
        $this->assertEquals('proverka-translita-123', StringHelper::str2url('Проверка транслита 123 '));
    }

    public function testHtml2text()
    {
        $this->assertEquals(
            "Заголовок\n Текст1 Текст2\nТекст3",
            StringHelper::html2text("<h1>Заголовок</h1>\n<div>Текст1\nТекст2</div>Текст3")
        );
    }
}
