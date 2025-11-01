<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class EmailRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'L\'adresse email ne peut pas être vide.'
            ),
            new Assert\Email(
                message: 'L\'adresse email {{ value }} n\'est pas une adresse valide.',
            ),
            new Assert\Length(
                max: 255,
                maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.',
            ),
        ];
    }
}