<?php

namespace WebComplete\core\utils\tree;

use WebComplete\core\entity\AbstractEntity;

trait TreeItemTrait
{
    /** @var self|null */
    protected $parent;
    /** @var self[] */
    protected $children = [];

    /**
     * @param self|AbstractEntity $item
     */
    public function setParent($item)
    {
        $this->parent = $item;
    }

    /**
     * @return self|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param self|AbstractEntity $item
     */
    public function addChild($item)
    {
        $this->children[$item->getId()] = $item;
    }

    /**
     * @return self[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
