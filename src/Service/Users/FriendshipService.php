<?php

namespace App\Service\Users;

use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FriendshipService
{
    public function __construct(
        private FriendshipRepository $friendshipRepository,
        private UsersService $usersService,
        private Security $security,
        private EntityManagerInterface $entityManager,
    ) {
    }



    public function verifyFriendshipExists(User $user1, User $user2): Friendship
    {
        $friendship = $this->friendshipRepository->findByIds($user1, $user2);
        if (!$friendship) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Vous n\'êtes pas ami avec cet utilisateur.');
        }

        return $friendship;
    }



    public function verifyNotAlreadyFriends(User $user1, User $user2): bool
    {
        $friendship = $this->friendshipRepository->findByIds($user1, $user2);
        if ($friendship) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Vous êtes déjà ami avec cet utilisateur.');
        }

        return true;
    }



    public function get(User $user1, User $user2): Friendship
    {
        return $this->verifyFriendshipExists($user1, $user2);
    }



    public function getAllPagination(int $page): PaginationInterface
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($page < 1) {
            $page = 1;
        }

        return $this->friendshipRepository->paginateFriends($user, $page);
    }



    public function remove(int $id): void
    {
        /** @var User $loggedInUser */
        $loggedInUser = $this->security->getUser();
        $targetedUser = $this->usersService->get($id);

        $friendship = $this->get($loggedInUser, $targetedUser);
        $this->entityManager->remove($friendship);
        $this->entityManager->flush();

        return;
    }



    public function areFriends(User $user1, User $user2): bool
    {
        return $this->friendshipRepository->relationExists($user1, $user2);
    }
}
