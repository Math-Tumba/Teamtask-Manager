<?php

namespace App\Service;

class ArrayHelper
{
    /**
     * Verifies recursively if an array and its subarrays contain only null values.
     */
    public function allValuesAreNull(array $array): bool
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                if (!$this->allValuesAreNull($value)) {
                    return false;
                }
            } elseif (!is_null($value)) {
                return false;
            }
        }

        return true;
    }
}
