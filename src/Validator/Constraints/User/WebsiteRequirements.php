<?php

namespace App\Validator\Constraints\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
final class WebsiteRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\Length(
                max: 255,
                maxMessage: 'Le lien ne peut pas dépasser 255 caractères.',
            ),
            new Assert\Regex(
                pattern: "/^(https?:\/\/)?(www\.)?([a-z0-9-]+\.)+[a-z]+(\/[a-z0-9_?%=-]+)*\/?$/i",
                htmlPattern: "/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]+(\/[a-zA-Z0-9_?%=-]+)*\/?$/",
                message: 'L\'URL doit être correcte (ex : mon-site.fr).',
            ),
        ];
    }
}
