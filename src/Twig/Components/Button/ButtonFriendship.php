<?php

namespace App\Twig\Components\Button;

use App\Service\Users\FriendshipService;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent(name: '_button_friendship', template: '_components/button/_button_friendship.html.twig')]
final class ButtonFriendship
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $userId;

    public function __construct(
        private FriendshipService $friendshipService,
    ) {
    }

    public function getFriendshipState() {
        return $this->friendshipService->getState($this->userId);
    }
}