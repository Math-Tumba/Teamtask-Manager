<?php

namespace App\Controller\Api\Users;

use App\Service\Users\FriendshipService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users/friends')]
class FriendController extends AbstractController {

    /**
     * 
     */
    #[Route(path: '/{id}', name: 'api_remove_friend', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove (
        int $id,
        FriendshipService $friendshipService, 
    ) : JsonResponse {

        $friendshipService->remove($id);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}