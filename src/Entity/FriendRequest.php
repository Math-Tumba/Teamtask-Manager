<?php

namespace App\Entity;

use App\Repository\FriendRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRequestRepository::class)]
#[ORM\Table(name: 'user_friend_request')]
class FriendRequest
{
    
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'friendRequestSent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $userSender = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'friendRequestReceived')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $userReceiver = null;

    public function __construct(User $userSender, User $userReceiver)
    {
        $this->userSender = $userSender;
        $this->userReceiver = $userReceiver;
    }

    public function getUserSender(): ?User
    {
        return $this->userSender;
    }

    public function getUserReceiver(): ?User
    {
        return $this->userReceiver;
    }
}
