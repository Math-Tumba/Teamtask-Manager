<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class UsernameRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'Le nom d\'utilisateur ne peut pas être vide.',
            ),
            new Assert\Length(
                max: 50,
                maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.',
            ),
        ];
    }
}