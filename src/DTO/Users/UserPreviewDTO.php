<?php 

namespace App\DTO\Users;

class UserPreviewDTO {

    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $country,
        public readonly string $profilePicture,
    ) {
    }
}