<?php

namespace App\Twig\Components\Pagination;

use App\Service\Users\FriendRequestsService;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent(name: '_pagination_friend_requests_sent', template: '_components/pagination/_pagination_friend_requests_sent.html.twig')]
final class PaginationFriendRequestsSent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public int $page_fr_sent = 1;

    public function __construct(
        private FriendRequestsService $friendRequestsService,
    ) {
    }

    public function getFriendRequests() {
        if ($this->page_fr_sent <= 0) {
            $this->page_fr_sent = 1;
        }
        return $this->friendRequestsService->getAllSentPagination($this->page_fr_sent);
    }
}