<?php

namespace App\Controller\Api\Users;

use App\DTO\Users\UserCreateDTO;
use App\DTO\Users\UserDetailDTO;
use App\DTO\Users\UserProfilePictureDTO;
use App\DTO\Users\UserUpdateDTO;
use App\OpenApi\JsonRequestBody\JsonRequestBodyFile;
use App\OpenApi\Parameter\IdParameter;
use App\OpenApi\Response\BadRequestResponse;
use App\OpenApi\Response\NotFoundResponse;
use App\OpenApi\Response\SuccessCreatedResponse;
use App\OpenApi\Response\SuccessNoContentResponse;
use App\OpenApi\Response\SuccessResponse;
use App\OpenApi\Response\ValidationErrorResponse;
use App\Service\CookieHelper;
use App\Service\Users\UsersService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[OA\Tag(
    name: 'Users',
)]
#[Route('/api/users')]
final class UserController extends AbstractController
{
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
    ): JsonResponse {
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
            new SuccessResponse(UserDetailDTO::class),
            new NotFoundResponse('User'),
        ],
    )]
    #[Route('/{id}', name: 'api_get_user', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function get(
        int $id,
        UsersService $usersService,
        SerializerInterface $serializer,
    ): JsonResponse {
        $user = $usersService->get($id);

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => ['users.index', 'users.detail']]);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }



    #[OA\Get(
        summary: 'Get your user\'s data.',
        responses: [
            new SuccessResponse(UserDetailDTO::class),
            new NotFoundResponse('User'),
        ],
    )]
    #[Route('/me', name: 'api_get_me', methods: ['GET'])]
    public function getMe(
        UsersService $usersService,
        SerializerInterface $serializer,
        Security $security,
    ): JsonResponse {
        /** @var User $loggedInUser */
        $loggedInUser = $security->getUser();
        $user = $usersService->get($loggedInUser->getId());

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => ['users.index', 'users.detail']]);

        return new JsonResponse($jsonUser, JsonResponse::HTTP_OK, [], true);
    }



    #[OA\Put(
        summary: 'Edit your user\'s data.',
        responses: [
            new SuccessNoContentResponse('Edited data has been saved.'),
            new NotFoundResponse('User'),
            new ValidationErrorResponse(),
        ],
    )]
    #[Route('/me', name: 'api_update_me', methods: ['PUT'])]
    public function update(
        UsersService $usersService,
        Security $security,
        #[MapRequestPayload(acceptFormat: 'json')]
        UserUpdateDTO $userDTO,
    ): JsonResponse {
        /** @var User $loggedInUser */
        $loggedInUser = $security->getUser();
        $id = $loggedInUser->getId();

        $usersService->update($id, $userDTO);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }



    #[OA\Post(
        summary: 'Edit your profile picture.',
        requestBody: new JsonRequestBodyFile('profilePicture'),
        responses: [
            new SuccessResponse(UserProfilePictureDTO::class, 'New profile picture has been saved and replaced.'),
            new BadRequestResponse('No file was uploaded.'),
            new NotFoundResponse('User'),
            new ValidationErrorResponse(),
        ],
    )]
    #[Route('/me/profile-picture', name: 'api_upload_profile_picture', methods: ['POST'])]
    public function uploadProfilePicture(
        Request $request,
        UsersService $usersService,
        Security $security,
    ): JsonResponse {
        /** @var User $loggedInUser */
        $loggedInUser = $security->getUser();
        $id = $loggedInUser->getId();

        $file = $request->files->get('profilePicture');
        $size = $file->getSize();
        if (!$file) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, 'Aucun fichier n\'a été envoyé.');
        }

        $user = $usersService->uploadProfilePicture($id, $file);

        return new JsonResponse(['profilePicture' => $user->getProfilePicture(), 'size' => $size], JsonResponse::HTTP_OK);
    }



    #[OA\Delete(
        summary: 'Delete your account, including linked files.',
        responses: [
            new SuccessNoContentResponse('User successfully deleted.'),
            new NotFoundResponse('User'),
        ],
    )]
    #[Route(path: '/me', name: 'api_delete_me', methods: ['DELETE'])]
    public function delete(
        UsersService $usersService,
        Security $security,
        Request $request,
        CookieHelper $cookieHelper,
    ): JsonResponse {
        /** @var User $loggedInUser */
        $loggedInUser = $security->getUser();
        $id = $loggedInUser->getId();

        $usersService->delete($id);

        $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        $cookieHelper->clearJwtCookies($response, $request->cookies->all());

        return $response;
    }
}
