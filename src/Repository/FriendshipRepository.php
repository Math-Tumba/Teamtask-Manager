<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Friendship;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    /**
     * 
     */
    public function relationExists(User $user1, User $user2) : bool {
        return (bool) $this->createQueryBuilder('uf')
            ->select('1')
            ->where('(uf.user1 = :user1 AND uf.user2 = :user2)')
            ->orWhere('(uf.user1 = :user2 AND uf.user2 = :user1)')
            ->getQuery() 
            ->setParameters([
                ':user1' => $user1,
                ':user2' => $user2
            ])
            ->getResult()
        ;
    }

    /**
     * 
     */
    public function findByIds(User $user1, User $user2) : Friendship {
        return $this->createQueryBuilder('uf')
            ->where('(uf.user1 = :user1 AND uf.user2 = :user2)')
            ->orWhere('(uf.user1 = :user2 AND uf.user2 = :user1)')
            ->getQuery() 
            ->setParameters([
                ':user1' => $user1,
                ':user2' => $user2
            ])
            ->getOneOrNullResult()
        ;
    }

    /**
     * 
     */
    public function paginateFriends(User $user, int $page) : PaginationInterface {
        return $this->paginator->paginate(
            $this->createQueryBuilder('uf')
                ->innerJoin('uf.user1', 'u')
                ->select('NEW App\\DTO\\Users\\UserPreviewDTO(u.id, u.username, u.country, u.profilePicture)')
                ->where('uf.user1 = :user')
                ->andWhere('uf.user2 = :user')
                ->getQuery()
                ->setParameter(':user', $user),
            $page,
            10,
        );
    }
}
