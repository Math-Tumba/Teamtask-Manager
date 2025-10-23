<?php 

namespace App\Twig\Components\Pagination;

use App\Service\Users\FriendshipService;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent(name: '_pagination_friends', template: '_components/pagination/_pagination_friends.html.twig')]
final class PaginationFriends
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public int $page = 1;

    public function __construct(
        private FriendshipService $friendshipService,
    ) {
    }

    public function getFriends() {
        if ($this->page <= 0) {
            $this->page = 1;
        }
        return $this->friendshipService->getAllPagination($this->page);
    }
}