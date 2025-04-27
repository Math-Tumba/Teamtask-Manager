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

    /**
     * Verifies if a friend request is already pending between two users.
     * 
     * @param User $userSender
     * @param User $userReceiver
     * 
     * @return bool 
     */
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

    /**
     * Retrieve users who sent a friend request to a specific user.
     * 
     * This query selects data based on the UserPreviewDTO. It is useful for displaying the pending 
     * friend requests received.
     * @param int $id the user who received the requests
     * 
     * @return array
     */
    public function findByFriendRequestReceived(int $id) : array {
        return $this->createQueryBuilder('ufr')
            ->innerJoin('ufr.userSender', 'u')
            ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
            ->where('ufr.userReceiver = :id')
            ->getQuery()
            ->setParameter(':id', $id)
            ->getResult();
    }

    /**
     * Retrieve users who received a friend request from a specific user.
     * 
     * This query selects data based on the UserPreviewDTO. It is useful for displaying the pending 
     * friend requests sent.
     * @param int $id the user who sent the requests
     * 
     * @return array
     */
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
