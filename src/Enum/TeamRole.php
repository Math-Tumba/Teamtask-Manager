<?php

namespace App\Enum;

/**
 * Represent the role a user can be linked to in a team.
 * /!\ This has nothing to do with the symfony native user roles.
 */
enum TeamRole: string
{
    case Host = 'Host';
    case Manager = 'Manager';
    case Collaborator = 'Collaborator';

    public function getLabel(): string
    {
        return match ($this) {
            self::Host => 'Propriétaire',
            self::Manager => 'Manager',
            self::Collaborator => 'Collaborateur',
        };
    }
}
