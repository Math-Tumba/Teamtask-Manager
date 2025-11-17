<?php 

namespace App\Controller\Api\Users;

use OpenApi\Attributes as OA;
use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserDetailDTO;
use App\DTO\Users\UserProfilePictureDTO;
use App\DTO\Users\UserUpdateDTO;
use App\Service\Users\UsersService;
use App\OpenApi\Parameter\IdParameter;
use App\OpenApi\Response\SuccessResponse;
use App\OpenApi\Response\NotFoundResponse;
use App\OpenApi\Response\ForbiddenResponse;
use App\OpenApi\Response\BadRequestResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\OpenApi\Response\SuccessCreatedResponse;
use App\OpenApi\Response\ValidationErrorResponse;
use App\OpenApi\Response\SuccessNoContentResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OpenApi\JsonRequestBody\JsonRequestBodyFile;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class UserController extends AbstractController {

    #[OA\Post(
        summary: 'Register a new user to the database.',
        responses: [
            new SuccessCreatedResponse(UserDetailDTO::class),
            new ValidationErrorResponse(),
        ],
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



    #[OA\Get(
        summary: 'Get user\'s detailed data.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessResponse(userDetailDto::class),
            new NotFoundResponse('User'),
        ],
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



    #[OA\Put(
        summary: 'Edit user\'s data.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('Edited data has been saved.'),
            new ForbiddenResponse('Not allowed to perform this action on another user.'),
            new NotFoundResponse('User'),
            new ValidationErrorResponse(),
        ],
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
 


    #[OA\Post(
        summary: 'Edit user\'s profile picture.',
        requestBody: new JsonRequestBodyFile('profilePicture'),
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessResponse(UserProfilePictureDTO::class, 'New profile picture has been saved and replaced.'),
            new BadRequestResponse('Either no file was uploaded, or the file doesn\'t respect the constraints.'),
            new ForbiddenResponse('Not allowed to perform this action on another user.'),
            new NotFoundResponse('User'),
        ],
    )]
    
    #[Route('/{id}/profile-picture', name: 'api_upload_profile_picture', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function uploadProfilePicture(
        int $id, 
        Request $request, 
        UsersService $usersService,
    ): JsonResponse {

        $file = $request->files->get('profilePicture');
        $size = $file->getSize();
        if (!$file) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Aucun fichier n\'a été envoyé.');
        }

        $user = $usersService->uploadProfilePicture($id, $file);
        return new JsonResponse(['profilePicture' => $user->getProfilePicture(), 'size' => $size], JsonResponse::HTTP_OK);
    }
 


    #[OA\Delete(
        summary: 'Delete the user, including linked files.',
        parameters: [
            new IdParameter(),
        ],
        responses: [
            new SuccessNoContentResponse('User successfully deleted.'),
            new ForbiddenResponse('Not allowed to perform this action on another user.'),
            new NotFoundResponse('User'),
        ],
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