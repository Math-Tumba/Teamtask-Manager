<?php 

namespace App\DTO\Users;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Friend request DTO used to get main data that permit to easily identify users.
 * 
 * Fields : id, username, country, profilePicture
 */
class FriendRequestStatusDTO {

    public function __construct(
        #[Assert\Choice(
            ['accept', 'decline'],
            message: '\'{{ value }}\' doesn\'t fit in the available choices : {{ choices }}'
        )]
        public ?string $status = null
    ) {
    }
}