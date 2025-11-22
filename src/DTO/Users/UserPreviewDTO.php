<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;

/**
 * User DTO used to get main data that permit to easily identify users.
 *
 * Fields : id, username, country, profilePicture
 */
readonly class UserPreviewDTO
{
    public function __construct(
        #[OA\Property(example: 15)]
        public int $id,

        #[OA\Property(example: 'JohnDoe')]
        public string $username,

        #[OA\Property(example: 'FR')]
        public string $country,

        #[OA\Property(example: '/uploads/profile-pictures/15.png')]
        public string $profilePicture,
    ) {
    }
}
