<?php

namespace App\Entity;

use App\Enum\TeamRole;
use App\Repository\UserInTeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

#[ORM\Entity(repositoryClass: UserInTeamRepository::class)]
class UserInTeam
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userInTeams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'usersInTeam')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $teamId = null;

    #[ORM\Column]
    private ?int $runningTasks = null;

    #[ORM\Column]
    private ?int $completedTasks = null;

    #[ORM\Column(enumType: TeamRole::class)]
    private ?TeamRole $teamRole = null;

    public function getTeamId(): ?Team
    {
        return $this->teamId;
    }

    public function setTeamId(int $teamId)
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $teamId)
    {
        $this->teamId = $teamId ;

        return $this;
    }

    public function getRunningTasks(): ?int
    {
        return $this->runningTasks;
    }

    public function setRunningTasks(int $runningTasks): static
    {
        $this->runningTasks = $runningTasks;

        return $this;
    }

    public function getCompletedTasks(): ?int
    {
        return $this->completedTasks;
    }

    public function setCompletedTasks(int $completedTasks): static
    {
        $this->completedTasks = $completedTasks;

        return $this;
    }

    public function getTeamRole(): ?TeamRole
    {
        return $this->teamRole;
    }

    public function setTeamRole(TeamRole $teamRole): static
    {
        $this->teamRole = $teamRole;

        return $this;
    }
}