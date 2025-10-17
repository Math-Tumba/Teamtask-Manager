<?php 

namespace App\Controller\Api\Users;

use App\Entity\User;
use OpenApi\Attributes as OA;
use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserUpdateDTO;
use App\Service\Users\UsersService;
use Nelmio\ApiDocBundle\Attribute\Model;
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
     * Register a new user to the database.
     */
    #[OA\Response(
        response: 201,
        description: 'New user successfully registered.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['users.index', 'users.detail']))
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'One or more required fields are empty, already used or are not the expected types.'
    )]
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
     * Get user's detailed data.
     */
    #[OA\Response(
        response: 200,
        description: 'User found.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['users.index', 'users.detail']))
        )
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
     * Edit user's data.
     */
    #[OA\Response(
        response: 204,
        description: 'Edited informations has been saved.'
    )]
    #[OA\Response(
        response: 403,
        description: 'It is not allowed to perform this action on another user.'
    )]
    #[OA\Response(
        response: 404,
        description: 'The user does not exist.'
    )]
    #[OA\Response(
        response: 422,
        description: 'One or more required fields are empty, already used or are not the expected types.'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The ID of the user.',
        schema: new OA\Schema(type: 'int')
    )]
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
     * Edit user's profile picture.
     */
    #[OA\Response(
        response: 200,
        description: 'New profile picture has been saved and replaced.'
    )]
    #[OA\Response(
        response: 400,
        description: 'Either no file was uploaded, or the file doesn\'t respect the constraints.'
    )]
    #[OA\Response(
        response: 403,
        description: 'It is not allowed to perform this action on another user.'
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
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['profilePicture'],
                properties: [
                    new OA\Property(
                        property: 'profilePicture',
                        description: 'The image file to upload.',
                        type: 'string',
                        format: 'binary'
                    )
                ]
            )
        )
    )]
    #[Route('/{id}/profile-picture', name: 'api_upload_profile_picture', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
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
     * Delete the user, including linked files.
     */
    #[OA\Response(
        response: 204,
        description: 'User successfully deleted.'
    )]
    #[OA\Response(
        response: 403,
        description: 'It is not allowed to perform this action on another user.'
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
    #[Route(path: '/{id}', name: 'api_delete_user', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete( 
        int $id, 
        UsersService $usersService,
    ) : JsonResponse {
        
        $usersService->delete($id); 
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}