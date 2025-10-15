<?php

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use App\Service\Users\FriendshipService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}