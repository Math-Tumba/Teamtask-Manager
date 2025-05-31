<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Service\UrlHelper;
use App\Service\ArrayHelper;
use App\Form\EditProfileFormType;
use App\DTO\Users\UserUpdateDTO;
use App\Service\Users\UsersService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/profile')] 
class ProfileController extends AbstractController
{

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    #[Route('/{id}', name: 'app_profile', requirements: ['id' => '\d+'])]
    public function show(
        int $id,
        UrlHelper $urlHelper, 
        ArrayHelper $arrayHelper,
        UsersService $usersService,
    ): Response {

        $currentUser = $this->getUser();

        $user = $usersService->verifyUserExists($id);

        $socialLinks = [
            'website' => [
                $user->getWebsite(),
                $urlHelper->getDomainName($user->getWebsite()),
            ],
            'github' => $user->getGithub(),
            'linkedin' => $user->getLinkedin()
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
    ): Response {

        /** @var User $user */
        $user = $this->getUser();
        $userDTO = new UserUpdateDTO($user);
        $form = $this->createForm(EditProfileFormType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $user->getId();
            $usersService->update($userId, $userDTO);

            $file = $form->get('profilePicture')->getData();            
            if ($file) {
                $usersService->uploadProfilePicture($userId, $file);
                $this->addFlash('success', 'La photo de profil a été mise à jour.');
            } 

            $this->addFlash('success', 'Votre profil a été mis à jour.');            
            return $this->redirectToRoute('app_profile', ['id' => $userId]);
        }

        return $this->render('profile/profile_update.html.twig', [
            'editProfileForm' => $form->createView(),
        ]);
    }

    

    #[Route('/friend-requests', name: 'app_profile_friend_requests')]
    public function showFriendRequests(Request $request) : Response {
        $page = $request->query->getInt('page', 1);
        // $friendRequestsReceived =  

        return $this->render('profile/profile_friend_requests.html.twig', [
            // 'friendRequestsReceived' => $friendRequestsReceived,
        ]);
    }
}