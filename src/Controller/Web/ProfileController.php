<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Service\UrlHelper;
use App\Service\ArrayHelper;
use App\Form\EditProfileFormType;
use App\DTO\Users\UserUpdateDTO;
use App\Service\Users\FriendRequestsService;
use App\Service\Users\UsersService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function showFriendRequests() : Response {
        return $this->render('profile/profile_friend_requests.html.twig', []);
    }



    #[Route('/friend-requests/components/pagination-friend-requests-received', name: 'component_pagination_friend_requests_received')]
    public function componentPaginationFriendRequestsReceived(
        Request $request,
        FriendRequestsService $friendRequestsService,
    ) : Response {

        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId(); 
        $pageFriendRequestReceived = $request->query->getInt('page_fr_received', 1);
        $friendRequestsReceived = $friendRequestsService->getFriendRequestsReceived($userId, $pageFriendRequestReceived);

        return $this->render('components/_pagination_friend_requests_received.html.twig', [
            'friendRequestsReceived' => $friendRequestsReceived,
        ]);
    } 



    #[Route('/friend-requests/components/pagination-friend-requests-sent', name: 'component_pagination_friend_requests_sent')]
    public function componentPaginationFriendRequestsSent(
        Request $request,
        FriendRequestsService $friendRequestsService,
    ) : Response {

        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId(); 
        $pageFriendRequestSent = $request->query->getInt('page_fr_sent', 1);
        $friendRequestsSent = $friendRequestsService->getFriendRequestsSent($userId, $pageFriendRequestSent);

        return $this->render('components/_pagination_friend_requests_sent.html.twig', [
            'friendRequestsSent' => $friendRequestsSent,
        ]);
    } 
}