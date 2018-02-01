<?php

namespace WebComplete\core\utils\tree;

use WebComplete\core\entity\AbstractEntity;

class Tree
{
    /**
     * @var AbstractEntity[]
     */
    protected $items = [];
    protected $root = [];
    protected $parentIds = [];
    protected $childrenIds = [];
    protected $parentField;
    protected $sortField;

    /**
     * @param AbstractEntity[] $items
     * @param string $parentField
     * @param string $sortField
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $items, string $parentField = 'parent_id', string $sortField = 'sort')
    {
        $this->parentField = $parentField;
        $this->sortField = $sortField;
        $this->initItems($items);
    }

    /**
     * @param $id
     *
     * @return null|AbstractEntity
     */
    public function getItem($id)
    {
        return $this->items[$id] ?? null;
    }

    /**
     * @return AbstractEntity[]
     */
    public function getAllItems(): array
    {
        return $this->items;
    }

    /**
     * @return AbstractEntity[]
     */
    public function getRootItems(): array
    {
        $result = [];
        foreach ($this->root as $id) {
            if ($item = $this->getItem($id)) {
                $result[$id] = $item;
            }
        }
        return $result;
    }

    /**
     * @param $id
     *
     * @return null|AbstractEntity
     */
    public function getParent($id)
    {
        return $this->getItem($this->parentIds[$id] ?? null);
    }

    /**
     * @param $id
     *
     * @return AbstractEntity[]
     */
    public function getChildren($id): array
    {
        $result = [];
        $childrenIds = (array)($this->childrenIds[$id] ?? []);
        foreach ($childrenIds as $childId) {
            if ($child = $this->getItem($childId)) {
                $result[$childId] = $child;
            }
        }
        return $result;
    }

    /**
     * @param array $items
     *
     * @throws \InvalidArgumentException
     */
    protected function initItems(array $items)
    {
        foreach ($items as $k => $item) {
            if (!$item instanceof AbstractEntity) {
                throw new \InvalidArgumentException('Item ' . $k . ' is not an instance of ' . AbstractEntity::class);
            }
            $this->items[$item->getId()] = $item;
        }

        \uasort($this->items, function (AbstractEntity $item1, AbstractEntity $item2) {
            return $item1->get($this->sortField) <=> $item2->get($this->sortField);
        });

        foreach ($this->items as $item) {
            $id = $item->getId();
            $parentId = $item->get($this->parentField);
            $this->parentIds[$id] = $parentId;
            isset($this->childrenIds[$parentId])
                ? $this->childrenIds[$parentId][] = $id
                : $this->childrenIds[$parentId] = [$id];
        }
    }
}
