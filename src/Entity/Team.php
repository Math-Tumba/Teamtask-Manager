<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// TO-DO : Revoir la classe

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $hostId = null;

    /**
     * @var Collection<int, UserInTeam>
     */
    #[ORM\OneToMany(targetEntity: UserInTeam::class, mappedBy: 'teamId', orphanRemoval: true)]
    private Collection $usersInTeam;

    public function __construct()
    {
        $this->usersInTeam = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getName(): ?string
    {
        return $this->name;
    }



    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }



    public function getHostId(): ?int
    {
        return $this->hostId;
    }



    public function setHostId(int $hostId): static
    {
        $this->hostId = $hostId;

        return $this;
    }



    /**
     * @return Collection<int, UserInTeam>
     */
    public function getUsersInTeam(): Collection
    {
        return $this->usersInTeam;
    }



    public function addUsersInTeam(UserInTeam $usersInTeam): static
    {
        if (!$this->usersInTeam->contains($usersInTeam)) {
            $this->usersInTeam->add($usersInTeam);
            $usersInTeam->setTeamId($this);
        }

        return $this;
    }



    public function removeUsersInTeam(UserInTeam $usersInTeam): static
    {
        if ($this->usersInTeam->removeElement($usersInTeam)) {
            // set the owning side to null (unless already changed)
            if ($usersInTeam->getTeamId() === $this) {
                $usersInTeam->setTeamId(null);
            }
        }

        return $this;
    }
}
