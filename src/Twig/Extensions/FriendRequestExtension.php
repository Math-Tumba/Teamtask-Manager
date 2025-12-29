<?php

namespace App\Twig\Extensions;

use App\Service\Users\FriendRequestsService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FriendRequestExtension extends AbstractExtension
{
    public function __construct(
        private FriendRequestsService $friendRequestsService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'pendingFriendRequestsReceived',
                [$this->friendRequestsService, 'countFriendRequestsReceived']
            ),
        ];
    }
}
