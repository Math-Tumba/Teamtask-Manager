<?php

namespace App\Controller\Web;

use App\DTO\Users\UserCreateDTO;
use App\Form\RegistrationFormType;
use App\Service\Users\UsersService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user')]
class AuthenticationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        Security $security, 
        UsersService $usersService
    ): Response {
        
        $userDTO = new UserCreateDTO();
        $form = $this->createForm(RegistrationFormType::class, $userDTO);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersService->register($userDTO);
            return $security->login($user, 'form_login', 'main');
        }
    
        return $this->render('authentication/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

 

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentication/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    } 



    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    { 
        throw new \LogicException();
    }
}