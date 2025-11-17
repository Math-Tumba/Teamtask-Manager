<?php

namespace App\EventListener;

use App\Service\CookieHelper;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener
{
    public function __construct(
        private CookieHelper $cookieHelper,
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
        $request = $event->getRequest();
        $this->cookieHelper->clearJwtCookies($response, $request->cookies->all());
    }
}
