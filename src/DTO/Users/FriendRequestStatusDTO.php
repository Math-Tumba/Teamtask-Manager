<?php

namespace App\DTO\Users;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Friend request DTO useful when handling friend request state.
 *
 * Fields : status
 */
final class FriendRequestStatusDTO
{
    public function __construct(
        #[Assert\Choice(
            ['accept', 'decline'],
            message: '\'{{ value }}\' ne fait partie des choix possibles : {{ choices }}',
        )]
        public ?string $status = null,
    ) {
    }
}
