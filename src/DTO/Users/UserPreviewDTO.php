<?php 

namespace App\DTO\Users;

/**
 * User DTO used to get main data that permit to easily identify users.
 * 
 * Fields : id, username, country, profilePicture
 */
class UserPreviewDTO {

    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $country,
        public readonly string $profilePicture,
    ) {
    }
}