<?php

namespace WebComplete\core\hydrator;


interface HydratorInterface
{

    /**
     * create or update object properties with data
     *
     * @param array $data
     * @param $objectOrClass
     * @param array $map fieldName: propertyName
     * @return object
     */
    public function hydrate(array $data, $objectOrClass, $map = []);

    /**
     * extract object properties to data
     *
     * @param object $object
     * @param array $map fieldName: propertyName
     * @return array
     */
    public function extract($object, $map = []);

}