<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\DTO\Users\FriendRequestStatusDTO;
use App\Service\Users\FriendRequestsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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
     * 
     */
    #[Route(path: '/received', name: 'api_get_friend_requests_received', methods: ['GET'])]
    public function getFriendRequestsReceived (
        FriendRequestsService $friendRequestsService,
        SerializerInterface $serializer,
        Request $request, 
    ) : JsonResponse {

        $friendRequests = $friendRequestsService->getFriendRequestsReceived($request->query->getInt('page', 1));

        $jsonFriendRequests = $serializer->serialize($friendRequests, 'json');
        return new JsonResponse($jsonFriendRequests, JsonResponse::HTTP_OK, [], true);
    }



    /**
     * 
     */
    #[Route(path: '/sent', name: 'api_get_friend_requests_sent', methods: ['GET'])]
    public function getFriendRequestsSent (
        FriendRequestsService $friendRequestsService,
        SerializerInterface $serializer,
        Request $request, 
    ) : JsonResponse {

        $friendRequests = $friendRequestsService->getFriendRequestsSent($request->query->getInt('page', 1));

        $jsonFriendRequests = $serializer->serialize($friendRequests, 'json');
        return new JsonResponse($jsonFriendRequests, JsonResponse::HTTP_OK, [], true);
    }



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
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) : JsonResponse {

        $statusDTO = $serializer->deserialize($request->getContent(), FriendRequestStatusDTO::class, 'json');
        
        $errors = $validator->validate($statusDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($statusDTO, $errors);
        }

        $status = $statusDTO->status;

        if ($status === 'accept') {
            $friendRequestsService->accept($id);
        } elseif ($status === 'decline') {
            $friendRequestsService->decline($id);
        }
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}