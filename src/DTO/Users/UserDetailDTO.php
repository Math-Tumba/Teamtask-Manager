<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;

/**
 * User DTO used to get detailed data.
 *
 * Fields : id, username, email, name, surname, country, website, github, linkedin, profilePicture
 */
readonly class UserDetailDTO
{
    public function __construct(
        #[OA\Property(example: 15)]
        public int $id,

        #[OA\Property(example: 'JohnDoe')]
        public string $username,

        #[OA\Property(example: 'john.doe@gmail.com')]
        public string $email,

        #[OA\Property(example: 'Doe')]
        public string $name,

        #[OA\Property(example: 'John')]
        public string $surname,

        #[OA\Property(example: 'FR')]
        public string $country,

        #[OA\Property(example: 'my-website.com')]
        public string $website,

        #[OA\Property(example: 'https://github.com/johndoe')]
        public string $github,

        #[OA\Property(example: 'https://www.linkedin.com/in/johndoe')]
        public string $linkedin,

        #[OA\Property(example: '/uploads/profile-pictures/15.png')]
        public string $profilePicture,
    ) {
    }
}
