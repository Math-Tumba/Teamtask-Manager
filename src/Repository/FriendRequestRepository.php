<?php

namespace App\Repository;

use App\Entity\FriendRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<FriendRequests>
 */
final class FriendRequestRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator,
    ) {
        parent::__construct($registry, FriendRequest::class);
    }



    /**
     * Verify unilaterally if a friend request is already pending between two users.
     */
    public function relationExists(User $userSender, User $userReceiver): bool
    {
        return (bool) $this->createQueryBuilder('ufr')
            ->select('1')
            ->where('ufr.userSender = :userSender')
            ->andWhere('ufr.userReceiver = :userReceiver')
            ->getQuery()
            ->setParameters([
                ':userSender' => $userSender,
                ':userReceiver' => $userReceiver,
            ])
            ->getResult()
        ;
    }



    /**
     * Retrieve users who sent a friend request to a specific user.
     *
     * This query selects data based on the UserPreviewDTO. It is useful for displaying the pending
     * friend requests received.
     *
     * @return PaginationInterface the items retrieved based on the page given
     */
    public function paginateFriendRequestsReceived(User $userReceiver, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('ufr')
                ->innerJoin('ufr.userSender', 'u')
                ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
                ->where('ufr.userReceiver = :userReceiver')
                ->getQuery()
                ->setParameter(':userReceiver', $userReceiver),
            $page,
            10,
            [
                'pageParameterName' => 'page_fr_received',
            ]
        );
    }



    /**
     * 
     */
    public function countFriendRequestsReceived(User $userReceiver): int
    {
        return $this->createQueryBuilder('ufr')
            ->select('COUNT(ufr.id)')
            ->where('ufr.userReceiver = :userReceiver')
            ->getQuery()
            ->setParameter(':userReceiver', $userReceiver)
            ->getSingleScalarResult();
    }



    /**
     * Retrieve users who received a friend request from a specific user.
     *
     * This query selects data based on the UserPreviewDTO. It is useful for displaying the pending
     * friend requests sent.
     *
     * @return PaginationInterface the items retrieved based on the page given
     */
    public function paginateFriendRequestsSent(User $userSender, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('ufr')
                ->innerJoin('ufr.userReceiver', 'u')
                ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
                ->where('ufr.userSender = :userSender')
                ->getQuery()
                ->setParameter(':userSender', $userSender),
            $page,
            10,
            [
                'pageParameterName' => 'page_fr_sent',
            ]
        );
    }



    /**
     * Delete mutual friend requests between 2 users.
     */
    public function deleteFriendRequestBothSides(User $user1, User $user2): int
    {
        return $this->createQueryBuilder('ufr')
            ->delete()
            ->where('(ufr.userSender = :user1 AND ufr.userReceiver = :user2)')
            ->orWhere('(ufr.userSender = :user2 AND ufr.userReceiver = :user1)')
            ->setParameters(new ArrayCollection([
                new Parameter(':user1', $user1),
                new Parameter(':user2', $user2),
            ]))
            ->getQuery()
            ->execute();
    }
}
