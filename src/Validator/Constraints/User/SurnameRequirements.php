<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class SurnameRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'Le prénom ne peut pas être vide.',
            ),
            new Assert\Length(
                max: 127,
                maxMessage: 'Le prénom ne peut pas dépasser {{limit}} caractères.',
            ),
        ];
    }
}
