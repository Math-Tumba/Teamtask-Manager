<?php

namespace App\Enum;

/**
 * 
 */
enum FriendshipState: string {
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