<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class NameRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'Le nom ne peut pas être vide.', 
            ),
            new Assert\Length(
                max: 127,
                maxMessage: 'Le nom ne peut pas dépasser {{limit}} caractères.',
            ),
        ];
    }
}