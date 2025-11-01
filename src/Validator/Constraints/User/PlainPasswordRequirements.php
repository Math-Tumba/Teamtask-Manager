<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class PlainPasswordRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'Veuillez entrer un mot de passe.',
            ),
            new Assert\Length(
                min: 6,
                minMessage: 'Veuillez entrer un mot de passe d\'au moins {{ limit }} caractères.',
                max: 4096,
                maxMessage: 'Veuillez entrer un mot de passe de moins de {{ limit }} caractères.',
            ),
        ];
    }
}