<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * TO-DO : Trouver un moyen d'enlever l'ID du DTO
 * 
 * User DTO used to update an user.
 * 
 * Fields : id, email, name, surname, country, website, github, linkedin
 */
#[UniqueEntity(fields: ['email'], entityClass: User::class, message: 'Cet email est déjà utilisé.', identifierFieldNames: ['id' => 'id'],)]
class UserUpdateDTO {

    #[Assert\NotBlank(
        message: 'L\'ID est requis.',
    )]
    public ?int $id = null;

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
    public ?string $email = null;

    #[Assert\NotBlank(
        message: 'Le nom ne peut pas être vide.', 
    )]
    #[Assert\Length(
        max: 127,
        maxMessage: 'Le nom ne peut pas dépasser {{limit}} caractères.',
    )]
    #[OA\Property(example: 'Doe')]
    public ?string $name = null;
    
    #[Assert\NotBlank(
        message: 'Le prénom ne peut pas être vide.',
    )]
    #[Assert\Length(
        max: 127,
        maxMessage: 'Le prénom ne peut pas dépasser {{limit}} caractères.',
    )]
    #[OA\Property(example: 'John')]
    public ?string $surname = null;

    #[Assert\NotBlank(
        message: 'La nationalité doit être renseignée.',
    )]
    #[Assert\Country(
        message: 'Ce code alpha2 ne correspond à aucun pays.',
    )]
    #[OA\Property(example: 'FR')]
    public ?string $country = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Le lien ne peut pas dépasser 255 caractères.',
    )]
    #[Assert\Regex(
        pattern:"/^(https?:\/\/)?(www\.)?([a-z0-9-]+\.)+[a-z]+(\/[a-z0-9_?%=-]+)*\/?$/i",
        htmlPattern:"/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]+(\/[a-zA-Z0-9_?%=-]+)*\/?$/",
        message: 'L\'URL doit être correcte (ex : mon-site.fr).',
    )]
    #[OA\Property(example: 'my-website.com')]
    public ?string $website = null;

    #[Assert\Regex(
        pattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/i",
        htmlPattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/",
        message: 'L\'URL doit être un lien valide vers Github (ex : https://github.com/utilisateur)',
    )]
    #[OA\Property(example: 'https://github.com/johndoe')]
    public ?string $github = null;

    #[Assert\Regex(
        pattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/i",
        htmlPattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/",
        message: 'L\'URL doit être un lien valide vers LinkedIn (ex : https://www.linkedin.com/in/utilisateur)',
    )]
    #[OA\Property(example: 'https://www.linkedin.com/in/johndoe')]
    public ?string $linkedin = null;

    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->id = $user->getId();
            $this->email = $user->getEmail();
            $this->name = $user->getName();
            $this->surname = $user->getSurname();
            $this->country = $user->getCountry();
            $this->website = $user->getWebsite();
            $this->github = $user->getGithub();
            $this->linkedin = $user->getLinkedin();
        }
    }
}