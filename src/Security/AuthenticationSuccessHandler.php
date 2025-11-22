<?php

namespace App\Security;

use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RouterInterface $router,
        private ParameterBagInterface $params,
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }



    /**
     * Handle success authentication event.
     *
     * Creates JWT cookies (BEARER and refresh_token) and saves the newly created refresh token
     * in database.
     *
     * @return RedirectResponse $response including JWT cookies
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        $jwt = $this->jwtManager->create($user);
        $urlAfterLogin = $request->getSession()->get('_security.main.target_path', $this->router->generate('app_home'));

        $ttl = 604800; // 1 semaine
        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($user, $ttl);
        $this->refreshTokenManager->save($refreshToken);

        $response = new RedirectResponse($urlAfterLogin);
        $response->headers->setCookie(
            Cookie::create('BEARER')
                ->withValue($jwt)
                ->withExpires(time() + 900) // 15 minutes
                ->withSameSite('strict')
                ->withPath('/')
                ->withDomain(null)
                ->withSecure(true)
                ->withHttpOnly(true)
                ->withPartitioned(false)
        );
        $response->headers->setCookie(
            Cookie::create('refresh_token')
                ->withValue($refreshToken)
                ->withExpires(time() + $ttl)
                ->withSameSite('strict')
                ->withPath('/')
                ->withDomain(null)
                ->withSecure(true)
                ->withHttpOnly(true)
                ->withPartitioned(false)
        );

        return $response;
    }
}
