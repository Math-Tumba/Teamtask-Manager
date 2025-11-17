<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use App\DTO\Users\UserPreviewDTO;
use App\OpenApi\Parameter\IdParameter;
use App\Service\Users\FriendshipService;
use App\OpenApi\Response\NotFoundResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\OpenApi\Response\SuccessNoContentResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use App\OpenApi\Model\Pagination\Parameter\PageParameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\OpenApi\Model\Pagination\Response\PaginationSuccessResponse;

#[OA\Tag(
    name: 'Friends',
)]
#[Route('/api/users/friends')]
class FriendController extends AbstractController {

    #[OA\Delete(
        summary: 'Remove the friendship with another user.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('Friendship successfully removed.'),
            new NotFoundResponse('Friendship'),
        ]
    )]
    #[Route(path: '/{id}', name: 'api_remove_friend', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove (
        int $id,
        FriendshipService $friendshipService, 
    ) : JsonResponse {

        $friendshipService->remove($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[OA\Get(
        summary: 'Get users who are friends with you.',
        parameters: [
            new PageParameter(),
        ],
        responses: [
            new PaginationSuccessResponse(UserPreviewDTO::class),
        ],
    )]
    #[Route(path: '', name: 'api_get_friends', methods: ['GET'])]
    public function getAllPagination (
        FriendshipService $friendshipService,
        SerializerInterface $serializer,
        Request $request, 
    ) : JsonResponse {

        $friends = $friendshipService->getAllPagination($request->query->getInt('page', 1));

        $jsonFriends = $serializer->serialize($friends, 'json');
        return new JsonResponse($jsonFriends, JsonResponse::HTTP_OK, [], true);
    }
}