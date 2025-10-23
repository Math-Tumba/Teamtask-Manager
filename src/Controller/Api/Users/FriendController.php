<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use App\DTO\Users\UserPreviewDTO;
use App\Service\Users\FriendshipService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users/friends')]
class FriendController extends AbstractController {

    /**
     * Remove the friendship with another user.
     */
    #[OA\Response(
        response: 204,
        description: 'Friendship successfully removed.'
    )]
    #[OA\Response(
        response: 404,
        description: 'No friendship exists between you and this user.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user.',
        schema: new OA\Schema(type: 'int')
    )]
    #[Route(path: '/{id}', name: 'api_remove_friend', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove (
        int $id,
        FriendshipService $friendshipService, 
    ) : JsonResponse {

        $friendshipService->remove($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    /**
     * Get users who are friends with you.
     */
    #[OA\Response(
        response: 200,
        description: 'List of users successfully found.',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: UserPreviewDTO::class))
                ),
                new OA\Property(
                    property: 'total',
                    type: 'int',
                    example: 120,
                ),
                new OA\Property(
                    property: 'page',
                    type: 'int',
                    example: 3,
                ),
                new OA\Property(
                    property: 'lastPage',
                    type: 'int',
                    example: 12,
                ),
            ]
        )
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        schema: new OA\Schema(type: 'int')
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