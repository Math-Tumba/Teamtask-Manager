<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class CountryRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(
                message: 'La nationalité doit être renseignée.',
            ),
            new Assert\Country(
                message: 'Ce code alpha2 ne correspond à aucun pays.',
            ),
        ];
    }
}
