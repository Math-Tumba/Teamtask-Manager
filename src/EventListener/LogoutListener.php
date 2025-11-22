<?php

namespace App\EventListener;

use App\Service\CookieHelper;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener
{
    public function __construct(
        private CookieHelper $cookieHelper,
    ) {
    }



    /**
     * Logout listener.
     *
     * After a user logouts, it will delete JWT and refresh tokens
     * from cookies. The refresh token is also removed from the database.
     */
    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        $this->cookieHelper->clearJwtCookies($response, $request->cookies->all());

        return;
    }
}
