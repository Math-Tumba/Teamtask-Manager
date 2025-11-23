<?php

namespace App\Service\Users;

use App\Entity\User;
use App\Enum\RelationshipState;
use Symfony\Bundle\SecurityBundle\Security;

final class RelationshipService
{
    public function __construct(
        private UsersService $usersService,
        private FriendRequestsService $friendRequestsService,
        private FriendshipService $friendshipService,
        private Security $security,
    ) {
    }

    

    /**
     * Get state of the relationship between the logged-in user and the targeted one.
     * 
     * @throws HttpException if the user doesn't exist.
     */
    public function getState(int $id): RelationshipState
    {
        /** @var User $loggedInUser */
        $loggedInUser = $this->security->getUser();
        $targetedUser = $this->usersService->get($id);

        if ($this->friendshipService->areFriends($loggedInUser, $targetedUser)) {
            return RelationshipState::Friends;
        }

        if ($this->friendRequestsService->hasPendingFriendRequestWith($loggedInUser, $targetedUser)) {
            return RelationshipState::Pending;
        }

        return RelationshipState::Strangers;
    }
}
