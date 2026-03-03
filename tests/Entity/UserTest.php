<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    public function testDefault()
    {
        $user = new User();

        $user
            ->setUsername('tototata')
            ->setEmail('tato@gmail.com')
            ->setPassword('hashPassword')
            ->setName('tata')
            ->setSurname('toto')
            ->setCountry('FR');

        $this->assertSame('tototata', $user->getUsername());
        $this->assertSame('tato@gmail.com', $user->getEmail());
        $this->assertSame('tata', $user->getName());
        $this->assertSame('toto', $user->getSurname());
        $this->assertSame('FR', $user->getCountry());
    }
}
