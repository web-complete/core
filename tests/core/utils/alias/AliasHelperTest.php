<?php

namespace WebComplete\core\utils\alias;

use Mvkasatkin\mocker\Mocker;

class AliasHelperTest extends \CoreTestCase
{

    public function tearDown()
    {
        parent::tearDown();
        AliasHelper::setInstance(new AliasService([]));
    }

    public function testSetAlias()
    {
        /** @var AliasService $a */
        $a = Mocker::create(AliasService::class, [
            Mocker::method('setAlias', 1, ['@some', '_value_'])
        ]);
        AliasHelper::setInstance($a);
        AliasHelper::setAlias('@some', '_value_');
    }

    public function testGet()
    {
        /** @var AliasService $a */
        $a = Mocker::create(AliasService::class, [
            Mocker::method('get', 1, ['@some', true])->returns('a')
        ]);
        AliasHelper::setInstance($a);
        AliasHelper::get('@some', true);
    }
}
