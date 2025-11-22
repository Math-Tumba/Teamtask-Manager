<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class LinkedInRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Regex(
                pattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/i",
                htmlPattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/",
                message: 'L\'URL doit être un lien valide vers LinkedIn (ex : https://www.linkedin.com/in/utilisateur)',
            ),
        ];
    }
}
