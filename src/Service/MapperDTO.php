<?php 

namespace App\Service;

use ReflectionClass;

class MapperDTO {

    public function map(Object $source) : Array {
        $res = [];
        $reflectionClass = new ReflectionClass($source);
        foreach($reflectionClass->getProperties() as $property) {
            $res[$property->getName()] = $property->getValue($source);
        }
        return $res;
    }
}