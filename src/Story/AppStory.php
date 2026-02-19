<?php

namespace App\Story;

use App\Factory\FriendRequestFactory;
use App\Factory\FriendshipFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        $users = UserFactory::createMany(25);
        $userTester = UserFactory::createOne([
            'username' => 'toto',
            'email' => 'toto@gmail.com',
            'name' => 'Tatou',
            'surname' => 'Toto',
            'country' => 'FR',
        ]);

        $usersFriendRequests = array_slice($users, 0, 15);
        $usersFriends = array_slice($users, 15, 5);

        foreach ($usersFriendRequests as $userFriendRequest) {
            FriendRequestFactory::requestFromTo($userFriendRequest, $userTester)
                ->create();
        }

        foreach ($usersFriends as $userFriend) {
            FriendshipFactory::friendshipBetween($userFriend, $userTester)
                ->create();
        }
    }
}
