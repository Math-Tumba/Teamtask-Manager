<?php

namespace App\Service\Users;

use App\Entity\User;
use App\Entity\FriendRequest;
use App\Service\Users\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FriendRequestsService {

    public function __construct(
        private FriendRequestRepository $friendRequestRepository,
        private EntityManagerInterface $entityManager,
        private UsersService $usersService,
        private Security $security,
    ) {
    }

    

    /**
     * Return a friend request if exists.
     *
     * @param User $userSender
     * @param User $userReceiver
     *
     * @throws HttpException if no friend request exists between each others.
     * 
     * @return FriendRequest [sender - receiver]
     */
    public function verifyFriendRequestExists(User $userSender, User $userReceiver) : FriendRequest {
        $friendRequest = $this->friendRequestRepository->findOneBy([
            'userSender' => $userSender,
            'userReceiver' => $userReceiver
        ]);
        if (!$friendRequest) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Il n\'y a pas de demande d\'ajout en attente avec cet utilisateur.');
        }
    
        return $friendRequest;
    }



    /**
     * @param User $userSender
     * @param User $userReceiver
     *
     * @throws HttpException if a friend request exists already and is pending.
     * 
     * @return bool
     */
    public function verifyFriendRequestNotPending(User $userSender, User $userReceiver) : bool {
        if ($this->friendRequestRepository->relationExists($userSender, $userReceiver)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Une demande d\'ajout est déjà en attente.');
        }

        return true;
    }



    /**
     * Get friendRequest by ID.
     *
     * @param int $id
     *
     * @throws HttpException if the user doesn't exist (from verifyFriendRequestExists()).
     *
     * @return FriendRequest
     */
    public function get(User $userSender, User $userReceiver) : FriendRequest {
        return $this->verifyFriendRequestExists($userSender, $userReceiver);
    }



    /**
     * Get a few friend request received page.
     *
     * @param int $page
     *
     * @return PaginationInterface
     * 
     * @see FriendRequestRepository::PaginateFriendRequestReceived()
     */
    public function getFriendRequestsReceived(int $page) : PaginationInterface {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->friendRequestRepository->PaginateFriendRequestsReceived($user, $page);
    }



    /**
     * Get a few friend request sent based page.
     *
     * @param int $page
     *
     * @return PaginationInterface
     * 
     * @see FriendRequestRepository::PaginateFriendRequestSent()
     */
    public function getFriendRequestsSent(int $page) : PaginationInterface {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->friendRequestRepository->PaginateFriendRequestsSent($user, $page);
    }



    /**
     * Send a friend request to another user.
     *
     * @param int $id the id of the receiver.
     *
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user sends a request to himself (from verifyNotSameUsers()).
     */
    public function sendFriendRequest(int $id) : void {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->usersService->verifyUserExists($id);
        $this->usersService->verifyNotSameUsers($userSender, $userReceiver);

        if($this->verifyFriendRequestNotPending($userSender, $userReceiver)) {
            $friendRequest = new FriendRequest($userSender, $userReceiver);
            
            $this->entityManager->persist($friendRequest);
            $this->entityManager->flush();
        }
    }



    /**
     * Cancel a friend request previously sent.
     *
     * @param int $id the id of the receiver.
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function cancelFriendRequest(int $id) : void {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);

        if($friendRequest) {            
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }
    }



    /**
     * Accept a friend request previously received.
     *
     * @param int $id the id of the sender.
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function acceptFriendRequest(int $id) : void {
        /** @var User $userSender */
        $userReceiver = $this->security->getUser(); 

        $userSender = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);
        if($friendRequest) {
            $userSender->addFriend($userReceiver);
            $this->friendRequestRepository->deleteFriendRequestBothSides($userSender, $userReceiver);
            $this->entityManager->persist($userSender);
            $this->entityManager->flush();
        }
    }



    /**
     * Decline a friend request previously received.
     *
     * @param int $id the id of the sender.
     *
     * @throws HttpException if the targeted user doesn't exist (from usersService->get()).
     *                       if the friend request doesn't exist (from get())
     */
    public function declineFriendRequest(int $id) : void {
        /** @var User $userReceiver */
        $userReceiver = $this->security->getUser(); 

        $userSender = $this->usersService->get($id);

        $friendRequest = $this->get($userSender, $userReceiver);
        if($friendRequest) {
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }
    }
}
