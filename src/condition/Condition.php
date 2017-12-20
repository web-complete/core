<?php

namespace WebComplete\core\condition;

class Condition
{
    const EQUALS            = 1;
    const NOT_EQUALS        = 2;
    const LESS_THAN         = 3;
    const GREATER_THAN      = 4;
    const LESS_OR_EQUALS    = 5;
    const GREATER_OR_EQUALS = 6;
    const BETWEEN           = 7;
    const LIKE              = 8;
    const IN                = 9;

    protected $conditions   = [];
    protected $sort         = [];
    protected $offsetValue;
    protected $limitValue;

    /**
     * @param array $conditions
     * @param string|null $sortField
     * @param int|null $sortDir
     * @param int|null $offset
     * @param int|null $limit
     */
    public function __construct(
        array $conditions = [],
        string $sortField = null,
        int $sortDir = null,
        int $offset = null,
        int $limit = null
    ) {
        if ($conditions = $this->parseConditionsArg($conditions)) {
            $this->conditions = $conditions;
        }
        if ($sortField !== null && $sortDir !== null) {
            $this->addSort($sortField, $sortDir);
        }
        if ($offset !== null) {
            $this->offset($offset);
        }
        if ($limit !== null) {
            $this->limit($limit);
        }
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addEqualsCondition(string $field, $value)
    {
        $this->conditions[] = [self::EQUALS, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addNotEqualsCondition(string $field, $value)
    {
        $this->conditions[] = [self::NOT_EQUALS, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addLessThanCondition(string $field, $value)
    {
        $this->conditions[] = [self::LESS_THAN, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addGreaterThanCondition(string $field, $value)
    {
        $this->conditions[] = [self::GREATER_THAN, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addLessOrEqualsCondition(string $field, $value)
    {
        $this->conditions[] = [self::LESS_OR_EQUALS, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function addGreaterOrEqualsCondition(string $field, $value)
    {
        $this->conditions[] = [self::GREATER_OR_EQUALS, $field, $value];
        return $this;
    }

    /**
     * @param $field
     * @param $valueFrom
     * @param $valueTo
     * @return $this
     */
    public function addBetweenCondition(string $field, $valueFrom, $valueTo)
    {
        $this->conditions[] = [self::BETWEEN, $field, $valueFrom, $valueTo];
        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @param bool $left
     * @param bool $right
     *
     * @return $this
     */
    public function addLikeCondition(string $field, $value, $left = true, $right = true)
    {
        $this->conditions[] = [self::LIKE, $field, $value, $left, $right];
        return $this;
    }

    /**
     * @param string $field
     * @param array $array
     * @param bool $isNumeric
     * @return $this
     */
    public function addInCondition(string $field, array $array, bool $isNumeric = false)
    {
        $this->conditions[] = [self::IN, $field, $array, $isNumeric];
        return $this;
    }

    /**
     * @param string $sortField
     * @param int $sortDir SORT FLAG (SORT_ASC, SORT_DESC)
     * @return $this
     */
    public function addSort(string $sortField, int $sortDir)
    {
        $this->sort[$sortField] = $sortDir;
        return $this;
    }

    /**
     * @param string $sortField
     * @param int $sortDir
     *
     * @return $this
     */
    public function setSort(string $sortField, int $sortDir)
    {
        $this->sort = [];
        return $this->addSort($sortField, $sortDir);
    }

    /**
     * @param $offset
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->offsetValue = $offset;
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limitValue = $limit;
        return $this;
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * @return null|int
     */
    public function getOffset()
    {
        return $this->offsetValue;
    }

    /**
     * @return null|int
     */
    public function getLimit()
    {
        return $this->limitValue;
    }

    /**
     * @param $conditions
     * @return array
     */
    protected function parseConditionsArg($conditions): array
    {
        $result = [];
        if (\is_array($conditions)) {
            foreach ($conditions as $k => $v) {
                if (\is_numeric($k) && \is_array($v)) {
                    $result[] = $v;
                }
                if (\is_string($k) && $v) {
                    if (\is_scalar($v)) {
                        $result[] = [self::EQUALS, $k, $v];
                    } elseif (\is_array($v)) {
                        $result[] = [self::IN, $k, $v];
                    }
                }
            }
        }
        return $result;
    }
}
