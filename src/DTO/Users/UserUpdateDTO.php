<?php

namespace App\DTO\Users;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity(fields: ['email'], entityClass: User::class, message: "Cet email est déjà utilisé.", identifierFieldNames: ['id' => 'id'],)]
class UserUpdateDTO {

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

    #[Assert\NotBlank(
        message: "L'ID est requis.",
    )]
    public ?int $id = null;

    #[Assert\NotBlank(
        message: "L'adresse email ne peut pas être vide.",
    )]
    #[Assert\Email(
        message: "L'adresse email {{ value }} n'est pas une adresse valide.",
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'adresse email ne peut pas dépasser {{ limit }} caractères.",
    )]
    public ?string $email = null;

    #[Assert\NotBlank(
        message: "Le nom ne peut pas être vide.", 
    )]
    #[Assert\Length(
        max: 127,
        maxMessage: "Le nom ne peut pas dépasser {{limit}} caractères.",
    )]
    public ?string $name = null;
    
    #[Assert\NotBlank(
        message: "Le prénom ne peut pas être vide.",
    )]
    #[Assert\Length(
        max: 127,
        maxMessage: "Le prénom ne peut pas dépasser {{limit}} caractères.",
    )]
    public ?string $surname = null;

    #[Assert\NotBlank(
        message: "La nationalité doit être renseignée.",
    )]
    #[Assert\Country(
        message: "Ce code alpha2 ne correspond à aucun pays.",
    )]
    public ?string $country = null;

    #[Assert\Length(
        max: 255,
        maxMessage: "Le lien ne peut pas dépasser 255 caractères.",
    )]
    #[Assert\Regex(
        pattern:"/^(https?:\/\/)?(www\.)?([a-z0-9-]+\.)+[a-z]+(\/[a-z0-9_?%=-]+)*\/?$/i",
        htmlPattern:"/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]+(\/[a-zA-Z0-9_?%=-]+)*\/?$/",
        message: "L'URL doit être correcte (ex : mon-site.fr).",
    )]
    public ?string $website = null;

    #[Assert\Regex(
        pattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/i",
        htmlPattern: "/^(https?:\/\/)?(www\.)?github\.com\/.+$/",
        message: "L'URL doit être un lien valide vers Github (ex : https://github.com/utilisateur)",
    )]
    public ?string $github = null;

    #[Assert\Regex(
        pattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/i",
        htmlPattern: "/^(https?:\/\/)?(www\.)?linkedin\.com\/(in)|(company)\/.*$/",
        message: "L'URL doit être un lien valide vers LinkedIn (ex : https://www.linkedin.com/in/utilisateur)",
    )]
    public ?string $linkedin = null;
}
