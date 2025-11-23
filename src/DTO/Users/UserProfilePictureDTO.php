<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;

/**
 * User DTO used to get data of profile picture.
 *
 * Fields : profilePicture, size
 */
final readonly class UserProfilePictureDTO
{
    public function __construct(
        #[OA\Property(example: '/uploads/profile-pictures/15.png')]
        public string $profilePicture,

        #[OA\Property(example: '54804')]
        public string $size,
    ) {
    }
}
