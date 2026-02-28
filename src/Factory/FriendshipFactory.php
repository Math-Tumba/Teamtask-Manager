<?php

namespace App\Factory;

use App\Entity\Friendship;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Friendship>
 */
final class FriendshipFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }



    public static function class(): string
    {
        return Friendship::class;
    }



    /**
     * Instanciate a friendship between two users.
     */
    public static function friendshipBetween(User $user1, User $user2)
    {
        return self::new([
            'user1' => $user1,
            'user2' => $user2,
        ]);
    }



    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'user1' => UserFactory::new(),
            'user2' => UserFactory::new(),
        ];
    }



    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Friendship $friendship): void {})
        ;
    }
}
