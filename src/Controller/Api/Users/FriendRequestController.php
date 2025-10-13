<?php

namespace App\Controller\Api\Users;

use App\Service\Users\FriendRequestsService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users/friend-requests')]
class FriendRequestController extends AbstractController {

    /**
     * 
     */
    #[Route(path: '/{id}', name: 'api_send_friend_request', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function send (
        int $id,
        FriendRequestsService $friendRequestsService, 
    ) : JsonResponse {

        $friendRequestsService->send($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * 
     */
    #[Route(path: '/{id}', name: 'api_cancel_friend_request', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function cancel (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->cancel($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * 
     */
    #[Route(path: '/{id}/accept', name: 'api_accept_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function accept (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->accept($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * 
     */
    #[Route(path: '/{id}/decline', name: 'api_decline_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function decline (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->decline($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}