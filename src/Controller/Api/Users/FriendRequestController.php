<?php

namespace App\Controller\Api\Users;

use App\Service\Users\FriendRequestsService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users/friend-request')]
class FriendRequestController extends AbstractController {

    #[Route(path: '/{id}', name: 'api_send_friend_request', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function sendFriendRequest (
        int $id,
        FriendRequestsService $friendRequestsService, 
    ) : JsonResponse {

        $friendRequestsService->sendFriendRequest($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[Route(path: '/{id}', name: 'api_cancel_friend_request', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function cancelFriendRequest (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->cancelFriendRequest($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[Route(path: '/{id}/accept', name: 'api_accept_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function acceptFriendRequest (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->acceptFriendRequest($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[Route(path: '/{id}/decline', name: 'api_decline_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function declineFriendRequest (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->declineFriendRequest($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}