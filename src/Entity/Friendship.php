<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
#[ORM\Table(name: '`user_friendship`')]
#[ORM\UniqueConstraint(name: 'unique_friendship', columns: ['user1_id', 'user2_id'])]
#[UniqueEntity(fields: ['user1', 'user2'], message: 'Ces utilisateurs sont déjà amis.')]
class Friendship
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'friends')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $user1 = null;

    #[ORM\ManyToOne(inversedBy: 'friendsWithMe')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'Cascade')]
    private ?User $user2 = null;

    public function __construct(User $user1, User $user2)
    {
        $this->user1 = $user1;
        $this->user2 = $user2;
    }



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getUser1(): ?User
    {
        return $this->user1;
    }



    public function getUser2(): ?User
    {
        return $this->user2;
    }
}
