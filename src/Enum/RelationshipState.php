<?php

namespace App\Enum;

/**
 * Represent the state of the relationship two users can have.
 */
enum RelationshipState: string
{
    case Strangers = 'Strangers';
    case Friends = 'Friends';
    case Pending = 'Pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::Strangers => 'Étrangers',
            self::Friends => 'Amis',
            self::Pending => 'Demande envoyée',
        };
    }
}
