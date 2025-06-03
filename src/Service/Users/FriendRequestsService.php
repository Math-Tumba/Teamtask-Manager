<?php

namespace App\Service\Users;

use App\Entity\User;
use App\Entity\FriendRequest;
use App\Service\Users\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FriendRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
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
     * Returns a friend request if exists.
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



    public function getFriendRequestsReceived(int $id, int $page) {
        $user = $this->usersService->verifyUserExists($id);
        return $this->friendRequestRepository->PaginateFriendRequestReceived($user, $page);
    }



    public function getFriendRequestsSent(int $id, int $page) {
        $user = $this->usersService->verifyUserExists($id);
        return $this->friendRequestRepository->PaginateFriendRequestSent($user, $page);
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
     * @throws HttpException if the targeted user doesn't exist (from verifyUserExists()).
     *                       if the logged-in user cancels a request to himself (from verifyNotSameUsers()).
     */
    public function cancelFriendRequest(int $id) {
        /** @var User $userSender */
        $userSender = $this->security->getUser(); 

        $userReceiver = $this->usersService->verifyUserExists($id);
        $this->usersService->verifyNotSameUsers($userSender, $userReceiver);

        $friendRequest = $this->verifyFriendRequestExists($userSender, $userReceiver);
        if($friendRequest) {
            $this->entityManager->remove($friendRequest);
            $this->entityManager->flush();
        }
    }
}
