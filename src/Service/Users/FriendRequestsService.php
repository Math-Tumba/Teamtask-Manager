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
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class FriendRequestsService
{
    public function __construct(
        private FriendRequestRepository $friendRequestRepository,
        private EntityManagerInterface $entityManager,
        private UsersService $usersService,
        private FriendshipService $friendshipService,
        private Security $security,
        private HubInterface $hub,
    ) {
    }



    /**
     * Return a friend request if exists.
     *
     * @return FriendRequest [sender - receiver]
     *
     * @throws HttpException if friend request doesn't exist
     */
    public function verifyFriendRequestExists(User $userSender, User $userReceiver): FriendRequest
    {
        /** @var FriendRequest|null $friendRequest */
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
     * Verify that a friend request is not already pending.
     *
     * * @throws HttpException if a friend request is pending
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
     * @throws HttpException if the friend request doesn't exist
     */
    public function get(User $userSender, User $userReceiver): FriendRequest
    {
        return $this->verifyFriendRequestExists($userSender, $userReceiver);
    }



    /**
     * Get paginated friend requests received.
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
     * Get paginated friend requests sent.
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
     * @throws HttpException if the user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user tried to send a request to himself.
     *                       if the users are already friends.
     *                       if the logged in user tries to send a friend request that already exists.
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

            $this->publishCountFriendRequestsReceived($userReceiver);
        }

        return;
    }



    /**
     * Cancel a friend request previously sent.
     *
     * @param int $id the id of the receiver
     *
     * @throws HttpException if the targeted user doesn't exist.
     *                       if the friend request doesn't exist.
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

        $this->publishCountFriendRequestsReceived($userReceiver);

        return;
    }



    /**
     * Accept a friend request previously received.
     *
     * @param int $id the id of the sender
     *
     * @throws HttpException if the targeted user doesn't exist.
     *                       if the friend request doesn't exist.
     */
    public function accept(int $id): void
    {
        /** @var User $userReceiver */
        $userReceiver = $this->security->getUser();

        $userSender = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);
        if ($friendRequest) {
            $friendship = new Friendship($userSender, $userReceiver);
            $this->entityManager->persist($friendship);
            $this->friendRequestRepository->deleteFriendRequestBothSides($userSender, $userReceiver);
            $this->entityManager->persist($userSender);
            $this->entityManager->flush();

            $this->publishCountFriendRequestsReceived($userReceiver);
        }

        return;
    }



    /**
     * Decline a friend request previously received.
     *
     * @param int $id the id of the sender
     *
     * @throws HttpException if the targeted user doesn't exist.
     *                       if the friend request doesn't exist.
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

            $this->publishCountFriendRequestsReceived($userReceiver);
        }

        return;
    }



    /**
     * Check if the sender has already sent a friend request to the targeted user.
     */
    public function hasPendingFriendRequestWith(User $userSender, User $userReceiver): bool
    {
        return $this->friendRequestRepository->relationExists($userSender, $userReceiver);
    }



    public function countFriendRequestsReceived(): int
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return 0;
        }

        /** @var User $userReceiver */
        $userReceiver = $this->security->getUser();

        return $this->friendRequestRepository->countFriendRequestsReceived($userReceiver);
    }



    public function publishCountFriendRequestsReceived(User $user): void
    {
        $count = $this->friendRequestRepository->countFriendRequestsReceived($user);

        $update = new Update(
            '/user/'.$user->getId().'/friend-requests',
            json_encode([
                'count' => $count,
            ]),
            true,
        );

        $this->hub->publish($update);

        return;
    }
}
