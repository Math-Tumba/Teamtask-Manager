<?php

namespace App\Service\Users;

use App\Entity\User;
use App\Enum\RelationshipState;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\SecurityBundle\Security;

class RelationshipService {

    public function __construct(
        private FriendshipRepository $friendshipRepository,
        private UsersService $usersService,
        private FriendRequestsService $friendRequestsService,
        private FriendshipService $friendshipService,
        private Security $security,
    ) {
    }

    /**
     * 
     */
    public function getState(int $id) : RelationshipState {
        /** @var User $loggedInUser */
        $loggedInUser = $this->security->getUser();
        $targetedUser = $this->usersService->get($id);
    
        if ($this->friendshipService->areFriends($loggedInUser, $targetedUser)) {
            return RelationshipState::Friends;
        }
        elseif ($this->friendRequestsService->hasPendingFriendRequestWith($loggedInUser, $targetedUser)) {
            return RelationshipState::Pending;
        }
        return RelationshipState::Strangers;
    }
}
