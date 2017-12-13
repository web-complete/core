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
        $this->set('name', $value);
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function setType($value)
    {
        $this->set('type', $value);
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function setActive($value)
    {
        $this->set('active', $value);
    }

    public function getActive()
    {
        return $this->get('active');
    }
}
