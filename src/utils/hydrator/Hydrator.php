<?php

namespace WebComplete\core\utils\hydrator;


class Hydrator implements HydratorInterface
{

    private $reflections = [];
    private $reflectionsPropertyList = [];

    /**
     * create or update object properties with data
     *
     * @param array $data
     * @param $objectOrClass
     * @param array $map fieldName: propertyName
     * @return object
     */
    public function hydrate(array $data, $objectOrClass, $map = [])
    {
        if(is_object($objectOrClass)) {
            $object = $objectOrClass;
            $reflection = $this->getReflection(get_class($object));
        }
        else {
            $reflection = $this->getReflection($objectOrClass);
            $object = $reflection->newInstanceWithoutConstructor();
        }

        if(!$map) {
            $map = array_keys($data);
            $map = array_combine($map, $map);
        }
        foreach ($map as $fieldName => $propertyName) {
            if ($reflection->hasProperty($propertyName) && isset($data[$fieldName])) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
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
     * @return array
     */
    public function extract($object, $map = [])
    {
        $result = [];

        $className = get_class($object);
        $reflection = $this->getReflection($className);

        if(!$map) {
            $propertyList = $this->getReflectionPropertyList($className);
            $map = array_combine($propertyList, $propertyList);
        }
        foreach ($map as $fieldName => $propertyName) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $result[$fieldName] = $property->getValue($object);
            }
        }

        return $result;
    }

    /**
     * @param $className
     * @return \ReflectionClass
     */
    protected function getReflection($className)
    {
        if (!isset($this->reflections[$className])) {
            $this->reflections[$className] = new \ReflectionClass($className);
        }
        return $this->reflections[$className];
    }

    /**
     * @param $className
     * @return array
     */
    protected function getReflectionPropertyList($className)
    {
        if(!isset($this->reflectionsPropertyList[$className])) {
            $this->reflectionsPropertyList[$className] = [];
            foreach ($this->getReflection($className)->getProperties() as $property) {
                $this->reflectionsPropertyList[$className][] = $property->getName();
            }
        }
        return $this->reflectionsPropertyList[$className];
    }

}