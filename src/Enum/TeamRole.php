<?php

namespace App\Enum;
    
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
