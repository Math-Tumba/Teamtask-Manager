<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Constraints\User as AppAssert;

/**
 * User DTO used to create a new user.
 * 
 * Fields : username, email, plainPassword, name, surname, country
 */
#[UniqueEntity(fields: ['username'], entityClass: User::class, message: 'Ce nom d\'utilisateur est déjà utilisé.')]
#[UniqueEntity(fields: ['email'], entityClass: User::class, message: 'Cet email est déjà utilisé.')]
class UserCreateDTO {

    public function __construct(
        
        #[AppAssert\UsernameRequirements()]
        #[OA\Property(example: 'JohnDoe')]
        public ?string $username = null,
    
        #[AppAssert\EmailRequirements()]
        #[OA\Property(example: 'john.doe@gmail.com')]
        public ?string $email = null,
    
        #[AppAssert\PlainPasswordRequirements()]
        #[OA\Property(example: 'passW')]
        public ?string $plainPassword = null,
    
        #[AppAssert\NameRequirements()]
        #[OA\Property(example: 'Doe')]
        public ?string $name = null,
        
        #[AppAssert\SurnameRequirements()]
        #[OA\Property(example: 'John')]
        public ?string $surname = null,
    
        #[AppAssert\CountryRequirements()]
        #[OA\Property(example: 'FR')]
        public ?string $country = null,
    ) {
    }
}