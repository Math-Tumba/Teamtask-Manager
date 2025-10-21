<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Friendship;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginator
    ){
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
    public function paginateFriends(User $user, int $page): PaginationInterface
    {
        $qb = $this->createQueryBuilder('uf')
            ->innerJoin('uf.user1', 'u1')
            ->innerJoin('uf.user2', 'u2')
            ->select('NEW App\\DTO\\Users\\UserPreviewDTO(
                CASE WHEN u1 = :user THEN u2.id ELSE u1.id END,
                CASE WHEN u1 = :user THEN u2.username ELSE u1.username END,
                CASE WHEN u1 = :user THEN u2.country ELSE u1.country END,
                CASE WHEN u1 = :user THEN u2.profilePicture ELSE u1.profilePicture END
            )')
            ->where('u1 = :user')
            ->orWhere('u2 = :user')
            ->setParameter(':user', $user);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            10
        );
    }
}
