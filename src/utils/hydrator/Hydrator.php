<?php

namespace WebComplete\core\utils\hydrator;

use ReflectionProperty;

class Hydrator implements HydratorInterface
{

    private $reflections = [];
    private $reflectionsPropertyList = [];

    /**
     * create or update object properties with data
     *
     * @param array $data
     * @param $objectOrClass
     * @param array|null $map fieldName: propertyName
     *
     * @return object
     * @throws \ReflectionException
     */
    public function hydrate(array $data, $objectOrClass, array $map = null)
    {
        if (\is_object($objectOrClass)) {
            $object = $objectOrClass;
            $reflection = $this->getReflection(\get_class($object));
        } else {
            $reflection = $this->getReflection($objectOrClass);
            $object = $reflection->newInstanceWithoutConstructor();
        }

        if (!\is_array($map) || !$map) {
            $map = \array_keys($data);
            $map = \array_combine($map, $map);
        }
        foreach ($map as $fieldName => $propertyName) {
            if (isset($data[$fieldName]) && $reflection->hasProperty($propertyName)) {
                $property = $this->getProperty($reflection, $propertyName);
                $property->setValue($object, $data[$fieldName]);
            }
        }

        return $object;
    }

    /**
     * extract object properties to data
     *
     * @param object $object
     * @param array $map fieldName: propertyName
     *
     * @return array
     * @throws \ReflectionException
     */
    public function extract($object, array $map = null): array
    {
        $result = [];

        $className = \get_class($object);
        $reflection = $this->getReflection($className);

        if (!\is_array($map) || !$map) {
            $propertyList = $this->getReflectionPropertyList($className);
            $map = \array_combine($propertyList, $propertyList);
        }
        foreach ($map as $fieldName => $propertyName) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $this->getProperty($reflection, $propertyName);
                $result[$fieldName] = $property->getValue($object);
            }
        }

        return $result;
    }

    /**
     * @param $className
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    protected function getReflection($className): \ReflectionClass
    {
        if (!isset($this->reflections[$className])) {
            $this->reflections[$className] = new \ReflectionClass($className);
        }
        return $this->reflections[$className];
    }

    /**
     * @param $className
     * @return array
     * @throws \ReflectionException
     */
    protected function getReflectionPropertyList($className): array
    {
        if (!isset($this->reflectionsPropertyList[$className])) {
            $this->reflectionsPropertyList[$className] = [];
            foreach ($this->getReflection($className)->getProperties() as $property) {
                $this->reflectionsPropertyList[$className][] = $property->getName();
            }
        }
        return $this->reflectionsPropertyList[$className];
    }

    /**
     * @param \ReflectionClass $reflection
     * @param $propertyName
     *
     * @return ReflectionProperty
     */
    protected function getProperty(\ReflectionClass $reflection, $propertyName): ReflectionProperty
    {
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property;
    }
}
