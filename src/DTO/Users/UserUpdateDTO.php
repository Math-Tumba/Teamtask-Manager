<?php

namespace App\DTO\Users;

use OpenApi\Attributes as OA;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Constraints\User as AppAssert;

/**
 * TO-DO : Trouver un moyen d'enlever l'ID du DTO
 * 
 * User DTO used to update an user.
 * 
 * Fields : id, email, name, surname, country, website, github, linkedin
 */
#[UniqueEntity(fields: ['email'], entityClass: User::class, message: 'Cet email est déjà utilisé.', identifierFieldNames: ['id' => 'id'],)]
class UserUpdateDTO {

    #[AppAssert\IdRequirements()]
    public ?int $id = null;

    #[AppAssert\EmailRequirements()]
    #[OA\Property(example: 'john.doe@gmail.com')]
    public ?string $email = null;

    #[AppAssert\NameRequirements()]
    #[OA\Property(example: 'Doe')]
    public ?string $name = null;
    
    #[AppAssert\SurnameRequirements()]
    #[OA\Property(example: 'John')]
    public ?string $surname = null;

    #[AppAssert\CountryRequirements()]
    #[OA\Property(example: 'FR')]
    public ?string $country = null;

    #[AppAssert\WebsiteRequirements()]
    #[OA\Property(example: 'my-website.com')]
    public ?string $website = null;

    #[AppAssert\GithubRequirements()]
    #[OA\Property(example: 'https://github.com/johndoe')]
    public ?string $github = null;

    #[AppAssert\LinkedInRequirements()]
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