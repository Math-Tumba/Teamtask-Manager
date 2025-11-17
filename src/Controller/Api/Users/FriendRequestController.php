<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use App\DTO\Users\UserPreviewDTO;
use App\OpenApi\Parameter\IdParameter;
use App\OpenApi\Response\NotFoundResponse;
use App\OpenApi\Response\BadRequestResponse;
use App\DTO\Users\FriendRequestStatusDTO;
use App\OpenApi\JsonRequestBody\JsonRequestBody;
use App\Service\Users\FriendRequestsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use App\OpenApi\Model\Pagination\Parameter\PageParameter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\OpenApi\Model\Pagination\Response\PaginationSuccessResponse;
use App\OpenApi\Response\SuccessNoContentResponse;
use App\OpenApi\Response\ConflictResponse;
use App\OpenApi\Response\ValidationErrorResponse;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[Route('/api/users/friend-requests')]
class FriendRequestController extends AbstractController {

    #[OA\Post(
        summary: 'Send a friend request to another user.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('Friend request successfully sent.'),
            new BadRequestResponse('A friend request has already been sent and is still pending.'),
            new NotFoundResponse('User'),
            new ConflictResponse('You cannot perform this action on yourself.'),
        ]
    )]
    #[Route(path: '/{id}', name: 'api_send_friend_request', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function send (
        int $id,
        FriendRequestsService $friendRequestsService, 
    ) : JsonResponse {

        $friendRequestsService->send($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[OA\Delete(
        summary: 'Cancel a pending friend request to another user.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('Friend request successfully canceled.'),
            new BadRequestResponse('There is no pending friend request to cancel.'),
            new NotFoundResponse('User'),
        ]
    )]
    #[Route(path: '/{id}', name: 'api_cancel_friend_request', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function cancel (
        int $id,
        FriendRequestsService $friendRequestsService,
    ) : JsonResponse {

        $friendRequestsService->cancel($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[OA\Get(
        summary: 'Get users who sent you a friend request.',
        parameters: [
            new PageParameter(),
        ],
        responses: [
            new PaginationSuccessResponse(UserPreviewDTO::class),
        ],
    )]
    #[Route(path: '/received', name: 'api_get_friend_requests_received', methods: ['GET'])]
    public function getAllReceivedPagination (
        FriendRequestsService $friendRequestsService,
        SerializerInterface $serializer,
        Request $request, 
    ) : JsonResponse {

        $friendRequests = $friendRequestsService->getAllReceivedPagination($request->query->getInt('page', 1));

        $jsonFriendRequests = $serializer->serialize($friendRequests, 'json');
        return new JsonResponse($jsonFriendRequests, JsonResponse::HTTP_OK, [], true);
    }



    #[OA\Get(
        summary: 'Get users who received a friend request from you.',
        parameters: [
            new PageParameter(),
        ],
        responses: [
            new PaginationSuccessResponse(UserPreviewDTO::class),
        ],
    )]
    #[Route(path: '/sent', name: 'api_get_friend_requests_sent', methods: ['GET'])]
    public function getAllSentPagination (
        FriendRequestsService $friendRequestsService,
        SerializerInterface $serializer,
        Request $request, 
    ) : JsonResponse {

        $friendRequests = $friendRequestsService->getAllSentPagination($request->query->getInt('page', 1));

        $jsonFriendRequests = $serializer->serialize($friendRequests, 'json');
        return new JsonResponse($jsonFriendRequests, JsonResponse::HTTP_OK, [], true);
    }



    #[OA\Put(
        summary: 'Update the friend request according to the choice (accept / decline).',
        requestBody: new JsonRequestBody(FriendRequestStatusDTO::class),
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('Friend request successfully handled.'),
            new NotFoundResponse('User'),
            new ValidationErrorResponse()
        ],
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