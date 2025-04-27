<?php 

namespace App\Service;

class ArrayHelper {
    
    /**
     * Verifies recursively if an array and its subarrays contain only null values
     * 
     * @param array $array
     * 
     * @return bool 
     */
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