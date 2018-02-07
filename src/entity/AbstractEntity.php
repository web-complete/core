<?php

namespace WebComplete\core\entity;

use WebComplete\core\utils\traits\TraitData;

abstract class AbstractEntity
{
    use TraitData {
        mapFromArray as traitMapFromArray;
        mapToArray as traitMapToArray;
    }

    protected $id;

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param array $data
     * @param bool $update
     */
    public function mapFromArray(array $data, bool $update = false)
    {
        $this->traitMapFromArray($data, $update);
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
    }

    /**
     * @return array
     */
    public function mapToArray(): array
    {
        $result = $this->traitMapToArray();
        $result['id'] = $this->getId();
        return $result;
    }
}
