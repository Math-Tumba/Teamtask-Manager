<?php

namespace App\Twig\Components;

use App\Service\Users\FriendRequestsService;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent(name: '_accordeon_friend_requests_received')]
final class AccordeonFriendRequestsReceived
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public int $page_fr_received = 1;

    public function __construct(
        private FriendRequestsService $friendRequestsService,
    ) {
    }

    public function getFriendRequests() {
        if ($this->page_fr_received <= 0) {
            $this->page_fr_received = 1;
        }
        return $this->friendRequestsService->getFriendRequestsReceived($this->page_fr_received);
    }
}