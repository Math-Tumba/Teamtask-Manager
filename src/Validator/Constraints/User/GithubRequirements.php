<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

#[\Attribute]
class GithubRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Regex(
                pattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/i",
                htmlPattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/",
                message: 'L\'URL doit être un lien valide vers Github (ex : https://github.com/utilisateur)',
            ),
        ];
    }
}