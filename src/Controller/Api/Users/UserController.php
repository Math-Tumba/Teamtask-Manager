<?php 

namespace App\Controller\Api\Users;

use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserUpdateDTO;
use App\Service\Users\UsersService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class UserController extends AbstractController {

    /**
     * 
     */
    #[Route('/register', name: 'api_register_user', methods: ['POST'])]
    public function register(
        SerializerInterface $serializer, 
        UrlGeneratorInterface $urlGenerator,
        UsersService $usersService,
        #[MapRequestPayload(acceptFormat: 'json')]
        UserCreateDTO $userDTO,
    ) : JsonResponse {

        $user = $usersService->register($userDTO);

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => ['users.index', 'users.detail']]);
        $location = $urlGenerator->generate('api_get_user', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, ['location' => $location], true);
    }



    /**
     * 
     */
    #[Route('/{id}', name: 'api_get_user', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function get(
        int $id, 
        UsersService $usersService,
        SerializerInterface $serializer
    ) : JsonResponse {
        
        $user = $usersService->get($id);
        
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => ['users.index', 'users.detail']]);
        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }



    /**
     * 
     */
    #[Route('/{id}', name: 'api_update_user', methods: ['PUT'], requirements: ['id' => Requirement::DIGITS])]
    public function update(
        int $id, 
        UsersService $usersService,
        #[MapRequestPayload(acceptFormat: 'json')]
        UserUpdateDTO $userDTO,
    ) : JsonResponse {

        $usersService->update($id, $userDTO);
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
 


    /**
     * 
     */
    #[Route('/{id}/upload-profile-picture', name: 'api_upload_profile_picture', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function uploadProfilePicture(
        int $id, 
        Request $request, 
        UsersService $usersService,
    ): JsonResponse {

        $file = $request->files->get('profilePicture');
        if (!$file) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Aucun fichier n\'a été envoyé.');
        }

        $user = $usersService->uploadProfilePicture($id, $file);
        return new JsonResponse(['success' => 'La photo de profil a été mise à jour.', 'profilePicture' => $user->getProfilePicture()], JsonResponse::HTTP_OK);
    }
 


    /**
     * 
     */
    #[Route(path: '/{id}', name: 'api_delete_user', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete( 
        int $id, 
        UsersService $usersService,
    ) : JsonResponse {
        
        $usersService->delete($id); 
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}