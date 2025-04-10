<?php 

namespace App\Service;

class ArrayHelper {
    
    public function allValuesAreNull(Array $array): bool {
        foreach ($array as $value) {
            if (is_array($value)) {
                if (!$this->allValuesAreNull($value)) {
                    return false;
                }
            }
            else if (!is_null($value)) {
                return false;
            }
        }
        return true;
    }
}