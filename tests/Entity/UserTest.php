<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    public function testUserIsInstanciatedWithDefaultValues(): void
    {
        $user = new User();

        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertEquals(User::getDefaultProfilePicturePath(), $user->getProfilePicture());
        $this->assertCount(0, $user->getFriendRequestsReceived());
        $this->assertCount(0, $user->getFriendRequestsSent());
        $this->assertCount(0, $user->getFriends());

        return;
    }



    public function testUsernameIsSameAsUserIdentifier(): void
    {
        $user = new User();
        $user->setUsername('toto');

        $this->assertEquals($user->getUserIdentifier(), $user->getUsername());

        return;
    }



    #[DataProvider('UserRolesProvider')]
    public function testUserHasAlwaysRoleUser(array $input, array $expected): void
    {
        $user = new User();
        $user->setRoles($input);

        $this->assertEqualsCanonicalizing($expected, $user->getRoles());

        return;
    }
    public static function UserRolesProvider(): array
    {
        return [
            'No role' => [[], ['ROLE_USER']],
            'Give admin role' => [['ROLE_ADMIN'], ['ROLE_ADMIN', 'ROLE_USER']],
            'Duplicated user role' => [['ROLE_USER'], ['ROLE_USER']],
        ];
    }
}
