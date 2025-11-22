<?php

namespace App\Service\Users;

use App\Entity\FriendRequest;
use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FriendRequestsService
{
    public function __construct(
        private FriendRequestRepository $friendRequestRepository,
        private EntityManagerInterface $entityManager,
        private UsersService $usersService,
        private FriendshipService $friendshipService,
        private Security $security,
    ) {
    }



    /**
     * Return a friend request if exists.
     *
     * @return FriendRequest [sender - receiver]
     *
     * @throws HttpException if no friend request exists between each others
     */
    public function verifyFriendRequestExists(User $userSender, User $userReceiver): FriendRequest
    {
        $friendRequest = $this->friendRequestRepository->findOneBy([
            'userSender' => $userSender,
            'userReceiver' => $userReceiver,
        ]);
        if (!$friendRequest) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Il n\'y a pas de demande d\'ajout en attente avec cet utilisateur.');
        }

        return $friendRequest;
    }



    /**
     * @throws HttpException if a friend request exists already and is pending
     */
    public function verifyFriendRequestNotPending(User $userSender, User $userReceiver): bool
    {
        if ($this->friendRequestRepository->relationExists($userSender, $userReceiver)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Une demande d\'ajout est déjà en attente.');
        }

        return true;
    }



    /**
     * Get friendRequest by ID.
     *
     * @throws HttpException if the user doesn't exist (from verifyFriendRequestExists())
     */
    public function get(User $userSender, User $userReceiver): FriendRequest
    {
        return $this->verifyFriendRequestExists($userSender, $userReceiver);
    }



    /**
     * Get a few friend request received page.
     *
     * @see FriendRequestRepository::paginateFriendRequestReceived()
     */
    public function getAllReceivedPagination(int $page): PaginationInterface
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($page < 1) {
            $page = 1;
        }

        return $this->friendRequestRepository->paginateFriendRequestsReceived($user, $page);
    }



    /**
     * Get a few friend request sent based page.
     *
     * @see FriendRequestRepository::paginateFriendRequestSent()
     */
    public function getAllSentPagination(int $page): PaginationInterface
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($page < 1) {
            $page = 1;
        }

        return $this->friendRequestRepository->paginateFriendRequestsSent($user, $page);
    }



    /**
     * Send a friend request to another user.
     *
     * @param int $id the id of the receiver
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user sends a request to himself (from verifyNotSameUsers()).
     */
    public function send(int $id): void
    {
        /** @var User $userSender */
        $userSender = $this->security->getUser();

        $userReceiver = $this->usersService->verifyUserExists($id);
        $this->usersService->verifyNotSameUsers($userSender, $userReceiver);

        if ($this->verifyFriendRequestNotPending($userSender, $userReceiver) && $this->friendshipService->verifyNotAlreadyFriends($userSender, $userReceiver)) {
            $friendRequest = new FriendRequest($userSender, $userReceiver);

            $this->entityManager->persist($friendRequest);
            $this->entityManager->flush();
        }

        return;
    }



    /**
     * Cancel a friend request previously sent.
     *
     * @param int $id the id of the receiver
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function cancel(int $id): void
    {
        /** @var User $userSender */
        $userSender = $this->security->getUser();

        $userReceiver = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);

        if ($friendRequest) {
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }

        return;
    }



    /**
     * Accept a friend request previously received.
     *
     * @param int $id the id of the sender
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function accept(int $id): void
    {
        /** @var User $userSender */
        $userReceiver = $this->security->getUser();

        $userSender = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);
        if ($friendRequest) {
            $friendship = new Friendship($userSender, $userReceiver);
            $this->entityManager->persist($friendship);
            $this->friendRequestRepository->deleteFriendRequestBothSides($userSender, $userReceiver);
            $this->entityManager->persist($userSender);
            $this->entityManager->flush();
        }

        return;
    }



    /**
     * Decline a friend request previously received.
     *
     * @param int $id the id of the sender
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function decline(int $id): void
    {
        /** @var User $userReceiver */
        $userReceiver = $this->security->getUser();

        $userSender = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);
        if ($friendRequest) {
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }

        return;
    }



    public function hasPendingFriendRequestWith(User $userSender, User $userReceiver): bool
    {
        return $this->friendRequestRepository->relationExists($userSender, $userReceiver);
    }
}
