<?php

namespace App\Enum;
    
/**
 * Represents the role a user can be linked to in a team.
 * /!\ This has nothing to do with the general user roles.
 */
enum TeamRole: string {
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