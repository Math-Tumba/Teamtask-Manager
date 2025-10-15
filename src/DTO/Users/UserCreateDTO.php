<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User DTO used to create a new user.
 * 
 * Fields : username, email, plainPassword, name, surname, country
 */
#[UniqueEntity(fields: ['username'], entityClass: User::class, message: 'Ce nom d\'utilisateur est déjà utilisé.')]
#[UniqueEntity(fields: ['email'], entityClass: User::class, message: 'Cet email est déjà utilisé.')]
class UserCreateDTO {

    public function __construct(
        #[Assert\NotBlank(
            message: 'Le nom d\'utilisateur ne peut pas être vide.',
        )]
        #[Assert\Length(
            max: 50,
            maxMessage: 'Le nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères.',
        )]
        #[OA\Property(example: 'JohnDoe')]
        public ?string $username = null,
    
        #[Assert\NotBlank(
            message: 'L\'adresse email ne peut pas être vide.',
        )]
        #[Assert\Email(
            message: 'L\'adresse email {{ value }} n\'est pas une adresse valide.',
        )]
        #[Assert\Length(
            max: 255,
            maxMessage: 'L\'adresse email ne peut pas dépasser {{ limit }} caractères.',
        )]
        #[OA\Property(example: 'john.doe@gmail.com')]
        public ?string $email = null,
    
        #[Assert\NotBlank(
            message: 'Veuillez entrer un mot de passe.', 
        )]
        #[Assert\Length(
            min: 6,
            minMessage: 'Veuillez entrer un mot de passe d\'au moins {{ limit }} caractères.',
            max: 4096,
            maxMessage: 'Veuillez entrer un mot de passe de moins de {{ limit }} caractères.',
        )]
        #[OA\Property(example: 'passW')]
        public ?string $plainPassword = null,
    
        #[Assert\NotBlank(
            message: 'Le nom ne peut pas être vide.', 
        )]
        #[Assert\Length(
            max: 127,
            maxMessage: 'Le nom ne peut pas dépasser {{limit}} caractères.',
        )]
        #[OA\Property(example: 'Doe')]
        public ?string $name = null,
        
        #[Assert\NotBlank(
            message: 'Le prénom ne peut pas être vide.',
        )]
        #[Assert\Length(
            max: 127,
            maxMessage: 'Le prénom ne peut pas dépasser {{limit}} caractères.',
        )]
        #[OA\Property(example: 'John')]
        public ?string $surname = null,
    
        #[Assert\NotBlank(
            message: 'La nationalité doit être renseignée.',
        )]
        #[Assert\Country(
            message: 'Ce code alpha2 ne correspond à aucun pays.',
        )]
        #[OA\Property(example: 'FR')]
        public ?string $country = null,
    ) {
    }
}