<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\DTO\Users\FriendRequestStatusDTO;
use App\Service\Users\FriendRequestsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users/friend-requests')]
class FriendRequestController extends AbstractController {

    /**
     * Send a friend request to another user.
     */
    #[OA\Response(
        response: 204,
        description: 'Friend request successfully sent.'
    )]
    #[OA\Response(
        response: 400,
        description: 'A friend request has already been sent and is still pending.'
    )]
    #[OA\Response(
        response: 404,
        description: 'The user does not exist.'
    )]
    #[OA\Response(
        response: 409,
        description: 'You cannot perform this action on yourself.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user.',
        schema: new OA\Schema(type: 'int')
    )]
    #[Route(path: '/{id}', name: 'api_send_friend_request', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function send (
        int $id,
        FriendRequestsService $friendRequestsService, 
    ) : JsonResponse {

        $friendRequestsService->send($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * Cancel a pending friend request to another user.
     */
    #[OA\Response(
        response: 204,
        description: 'Friend request successfully canceled.'
    )]
    #[OA\Response(
        response: 400,
        description: 'There is no pending friend request to cancel.'
    )]
    #[OA\Response(
        response: 404,
        description: 'The user does not exist.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user.',
        schema: new OA\Schema(type: 'int')
    )]
    #[Route(path: '/{id}', name: 'api_cancel_friend_request', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function cancel (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->cancel($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * TO-DO : Passer cette route en commun avec 'decline' et ajouter un 'body: status -> accept / decline'
     */
    // #[Route(path: '/{id}/accept', name: 'api_accept_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    // public function accept (
    //     int $id,
    //     FriendRequestsService $friendRequestsService,
    // ) : JsonResponse {

    //     $friendRequestsService->accept($id);
    //     return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    // }



    /**
     * TO-DO : Passer cette route en commun avec 'accept' et ajouter un 'body: status -> accept / decline'
     */
    // #[Route(path: '/{id}/decline', name: 'api_decline_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    // public function decline (
    //     int $id,
    //     FriendRequestsService $friendRequestsService,
    // ) : JsonResponse {

    //     $friendRequestsService->decline($id);
    //     return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    // }



    /**
     * Update the friend request according to the choice (accept / decline).
     */
    #[OA\Response(
        response: 204,
        description: 'Friend request successfully handled.'
    )]
    #[OA\Response(
        response: 400,
        description: 'Either no status was written in the request\'s body, or it doesn\'t respect the enabled choices.'
    )]
    #[OA\Response(
        response: 404,
        description: 'The user does not exist.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user.',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\RequestBody(
        content: new Model(type: FriendRequestStatusDTO::class)
    )]
    #[Route(path: '/{id}', name: 'api_update_status_friend_request', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function status (
        int $id,
        FriendRequestsService $friendRequestsService,
        Request $request,
    ) : JsonResponse {

        $statusData = json_decode($request->getContent(), true);
        $status = $statusData['status'] ?? null;

        if (!in_array($status, ['accept', 'decline'], true)) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Le choix renseigné est incorrect (accept / decline).');
        }

        if ($status === 'accept') {
            $friendRequestsService->accept($id);
        } elseif ($status === 'decline') {
            $friendRequestsService->decline($id);
        }
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}