<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[UniqueEntity(fields: ["username"], message: "Ce nom d'utilisateur est déjà utilisé.")]
#[UniqueEntity(fields: ["email"], message: "Cet email est déjà utilisé.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private const PROFILE_PICTURES_PATH = "/uploads/profile-pictures";
    private const DEFAULT_PROFILE_PICTURE_PATH = self::PROFILE_PICTURES_PATH . "/default.png";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column()]
    #[Groups(["users.index"])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(["users.index", "users.detail"])]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(["users.detail"])]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var string The temporary password
     */
    // #[Groups(["users.temp_credentials"])]
    // private ?string $plainPassword = null;

    #[ORM\Column(length: 127)]
    #[Groups(["users.detail"])]
    private ?string $name = null;

    #[ORM\Column(length: 127)]
    #[Groups(["users.detail"])]
    private ?string $surname = null;

    #[ORM\Column(length: 3)]
    #[Groups(["users.index", "users.detail"])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["users.detail"])]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["users.detail"])]
    private ?string $github = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["users.detail"])]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255)]
    #[Groups(["users.index", "users.detail"])]
    private ?string $profilePicture = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, UserInTeam>
     */
    #[ORM\OneToMany(targetEntity: UserInTeam::class, mappedBy: "userId", orphanRemoval: true)]
    private Collection $userInTeams;

    /**
     * @var Collection<int, FriendRequest>
     */
    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'userSender')]
    private Collection $friendRequestSent;

    /**
     * @var Collection<int, FriendRequest>
     */
    #[ORM\OneToMany(targetEntity: FriendRequest::class, mappedBy: 'userReceiver')]
    private Collection $friendRequestReceived;
    
    /**
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: "friendsWithMe")]
    #[ORM\JoinTable(name: "user_friend")]
    private Collection $friends;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: "friends")]
    private Collection $friendsWithMe;


    public function __construct()
    {
        $this->roles = ["ROLE_USER"];
        $this->profilePicture = self::getDefaultProfilePicturePath();

        $this->userInTeams = new ArrayCollection();
        $this->friendRequestSent = new ArrayCollection();
        $this->friendRequestReceived = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
    }

    public static function getDefaultProfilePicturePath() {
        return self::DEFAULT_PROFILE_PICTURE_PATH;
    }

    public static function getProfilePicturesPath() {
        return self::PROFILE_PICTURES_PATH;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }
    
    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): static
    {
        $this->github = $github;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = "ROLE_USER";

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserInTeams(): Collection
    {
        return $this->userInTeams;
    }

    public function addUserInTeam(UserInTeam $userInTeam): static
    {
        if (!$this->userInTeams->contains($userInTeam)) {
            $this->userInTeams->add($userInTeam);
            $userInTeam->setUserId($this->id);
        }

        return $this;
    }

    public function removeUserInTeam(UserInTeam $userInTeam): static
    {
        if ($this->userInTeams->removeElement($userInTeam)) {
            // set the owning side to null (unless already changed)
            if ($userInTeam->getUserId() === $this) {
                // $userInTeam->setUserId(null);
                // COMPLETER
            }
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriendRequestsSent(): Collection
    {
        return $this->friendRequestSent;
    }

    public function sendFriendRequest(self $receiver): static
    {
        if (!$this->friendRequestSent->contains($receiver)) {
            $this->friendRequestSent->add($receiver);
        }

        return $this; 
    }

    public function cancelFriendRequest(self $receiver): static
    {
        $this->friendRequestSent->removeElement($receiver);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriendRequestsReceived(): Collection
    {
        return $this->friendRequestReceived;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $newFriend) : static 
    {
        // FAIRE DES TESTS POUR VOIR COMMENT EST FAIT LA TABLE
        $this->friends->add($newFriend);
        // $newFriend->friends->add($this);

        $this->cancelFriendRequest($newFriend);
        // $newFriend->cancelFriendRequest($this);

        return $this;
    }

    public function removeFriend(self $removedFriend) : static 
    {
        $this->friends->removeElement($removedFriend);
        $removedFriend->friends->removeElement($this);

        return $this;
    }
}