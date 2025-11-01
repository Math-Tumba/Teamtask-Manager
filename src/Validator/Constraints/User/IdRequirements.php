<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class IdRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'L\'ID est requis.'
            ),
        ];
    }
}