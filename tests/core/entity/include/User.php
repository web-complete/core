<?php

namespace tests\entity;

use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\utils\typecast\Cast;

class User extends AbstractEntity
{

    /**
     * @return array
     */
    public static function fields(): array
    {
        return [
            'name' => Cast::STRING,
            'type' => Cast::STRING,
            'active' => Cast::BOOL,
        ];
    }

    public function setName($value)
    {
        $this->setField('name', $value);
    }

    public function getName()
    {
        return $this->getField('name');
    }

    public function setType($value)
    {
        $this->setField('type', $value);
    }

    public function getType()
    {
        return $this->getField('type');
    }

    public function setActive($value)
    {
        $this->setField('active', $value);
    }

    public function getActive()
    {
        return $this->getField('active');
    }
}
