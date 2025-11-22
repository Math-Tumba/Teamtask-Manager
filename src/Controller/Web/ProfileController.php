<?php

namespace App\Controller\Web;

use App\DTO\Users\UserUpdateDTO;
use App\Entity\User;
use App\Form\EditProfileFormType;
use App\Service\ArrayHelper;
use App\Service\UrlHelper;
use App\Service\Users\UsersService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user/profile')]
class ProfileController extends AbstractController
{
    #[Route('/{id}', name: 'app_profile', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        UrlHelper $urlHelper,
        ArrayHelper $arrayHelper,
        UsersService $usersService,
    ): Response {
        $currentUser = $this->getUser();
        $user = $usersService->get($id);

        $socialLinks = [
            'website' => [
                $user->getWebsite(),
                $urlHelper->getDomainName($user->getWebsite()),
            ],
            'github' => $user->getGithub(),
            'linkedin' => $user->getLinkedin(),
        ];

        $hasSocialLinks = !$arrayHelper->allValuesAreNull($socialLinks);

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'isSelf' => $user === $currentUser,
            'hasSocialLinks' => $hasSocialLinks,
            'socialLinks' => $socialLinks,
        ]);
    }



    #[Route('/update', name: 'app_profile_update')]
    public function edit(
        Request $request,
        UsersService $usersService,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $userDTO = new UserUpdateDTO($user);
        $form = $this->createForm(EditProfileFormType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $user->getId();

            $user
                ->setEmail($userDTO->email)
                ->setName($userDTO->name)
                ->setSurname($userDTO->surname)
                ->setCountry($userDTO->country)
                ->setWebsite($userDTO->website)
                ->setGithub($userDTO->github)
                ->setLinkedin($userDTO->linkedin);

            $violations = $validator->validate($user);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $property = $violation->getPropertyPath();
                    if ($form->has($property)) {
                        $form->get($property)->addError(new FormError($violation->getMessage()));
                    } else {
                        $form->addError(new FormError($violation->getMessage()));
                    }
                }
            } else {
                $file = $form->get('profilePicture')->getData();
                if ($file) {
                    $usersService->uploadProfilePicture($userId, $file);
                    $this->addFlash('success', 'La photo de profil a été mise à jour.');
                }

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a été mis à jour.');

                return $this->redirectToRoute('app_profile', ['id' => $userId]);
            }
        }

        return $this->render('profile/profile_update.html.twig', [
            'editProfileForm' => $form,
        ]);
    }



    #[Route('/friend-requests', name: 'app_profile_friend_requests')]
    public function showFriendRequests(): Response
    {
        return $this->render('profile/profile_friend_requests.html.twig', []);
    }



    #[Route('/friends', name: 'app_profile_friends')]
    public function showFriends(): Response
    {
        return $this->render('profile/profile_friends.html.twig', []);
    }
}
