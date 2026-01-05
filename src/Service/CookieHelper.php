<?php

namespace App\Service;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;

final class CookieHelper
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }



    /**
     * Clear the bearer and the refresh_token cookies, and delete the refresh_token from database.
     */
    public function clearJwtCookies(Response $response, array $cookies): void
    {
        $response->headers->clearCookie('BEARER', '/', null, true, true, 'strict');

        $refreshTokenCookie = $cookies['refresh_token'] ?? null;
        if ($refreshTokenCookie) {
            $response->headers->clearCookie('refresh_token', '/', null, true, true, 'strict');

            $refreshToken = $this->refreshTokenManager->get($refreshTokenCookie);
            if ($refreshToken) {
                $this->refreshTokenManager->delete($refreshToken);
            }
        }
    }
}
