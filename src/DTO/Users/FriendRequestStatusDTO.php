<?php

namespace App\DTO\Users;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Friend request DTO used to get main data that permit to easily identify users.
 *
 * Fields : status
 */
class FriendRequestStatusDTO
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
