<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FriendRequestRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
#[ORM\Table(name: 'user_friend_request')]
#[UniqueEntity(fields: ['userSender', 'userReceiver'], message: 'Une demande existe déjà entre ces deux utilisateurs.')]
class FriendRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friendRequestSent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $userSender = null;

    #[ORM\ManyToOne(inversedBy: 'friendRequestReceived')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $userReceiver = null;

    public function __construct(User $userSender, User $userReceiver) {
        $this->userSender = $userSender;
        $this->userReceiver = $userReceiver;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUserSender(): ?User {
        return $this->userSender;
    }

    public function getUserReceiver(): ?User {
        return $this->userReceiver;
    }
}