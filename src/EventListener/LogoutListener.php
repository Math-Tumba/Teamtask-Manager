<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {
    }

    /**
     * Logout listener
     * 
     * After a user logouts, it will delete JWT and refresh tokens
     * from cookies. The refresh token is also removed from the database.
     * @param LogoutEvent $event
     */
    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->clearCookie('BEARER', '/', null, true, true, 'strict');

        $refreshTokenCookie = $event->getRequest()->cookies->get('refresh_token');
        if ($refreshTokenCookie) {
            $response->headers->clearCookie('refresh_token', '/', null, true, true, 'strict');

            $refreshToken = $this->refreshTokenManager->get($refreshTokenCookie);
            if ($refreshToken) {
                $this->refreshTokenManager->delete($refreshToken);
            }
        }
    }
}
