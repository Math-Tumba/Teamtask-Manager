<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FriendRequests>
 */
class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendRequest::class);
    }

    public function relationExists(User $userSender, User $userReceiver) : bool {
        return (bool) $this->createQueryBuilder('ufr')
            ->select('1')
            ->where('ufr.userSender = :userSender')
            ->andWhere('ufr.userReceiver = :userReceiver')
            ->getQuery()
            ->setParameters([
                ':userSender' => $userSender,
                ':userReceiver' => $userReceiver
            ])
            ->getResult()
        ;
    }

    public function findByFriendRequestReceived(int $id) : array {
        return $this->createQueryBuilder('ufr')
            ->innerJoin('ufr.userSender', 'u')
            ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
            ->where('ufr.userReceiver = :id')
            ->getQuery()
            ->setParameter(':id', $id)
            ->getResult();
    }

    public function findByFriendRequestSent(int $id) : array {
        return $this->createQueryBuilder('ufr')
            ->innerJoin('ufr.userReceiver', 'u')
            ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
            ->where('ufr.userSender = :id')
            ->getQuery()
            ->setParameter(':id', $id)
            ->getResult();
    }
}
