<?php

namespace WebComplete\core\utils\typecast\type;

use WebComplete\core\utils\typecast\Cast;
use WebComplete\core\utils\typecast\Exception;

class Factory
{

    /**
     * @param string|TypeClosure|\Closure $type
     *
     * @return CastInterface
     * @throws \WebComplete\core\utils\typecast\Exception
     */
    public function createType($type): CastInterface
    {
        $result = null;
        if ($type instanceof \Closure) {
            $result = new TypeClosure($type);
        } elseif (\in_array($type, Cast::TYPES, true)) {
            switch ($type) {
                case Cast::INT:
                    $result = new TypeInt();
                    break;
                case Cast::BOOL:
                    $result = new TypeBool();
                    break;
                case Cast::FLOAT:
                    $result = new TypeFloat();
                    break;
                case Cast::STRING:
                    $result = new TypeString();
                    break;
                case Cast::BINARY:
                    $result = new TypeBinary();
                    break;
                case Cast::OBJECT:
                    $result = new TypeObject();
                    break;
                case Cast::UNSET:
                    $result = new TypeUnset();
                    break;
                case Cast::ARRAY:
                    $result = new TypeArray();
                    break;
            }
        } elseif ($type instanceof CastInterface) {
            $result = $type;
        }

        if ($result === null) {
            throw new Exception('Type not found');
        }

        return $result;
    }

    /**
     * @param $type
     *
     * @return TypeArrayOfType
     * @throws \WebComplete\core\utils\typecast\Exception
     */
    public function createArrayOfType($type): TypeArrayOfType
    {
        return new TypeArrayOfType($type);
    }

    /**
     * @param array $config
     *
     * @return TypeArrayOfType|null
     * @throws \WebComplete\core\utils\typecast\Exception
     */
    public function checkArrayOfType(array $config)
    {
        if (\count($config) === 1 && isset($config[0])) {
            $type = $this->createType($config[0]);
            return $this->createArrayOfType($type);
        }
        return null;
    }
}
