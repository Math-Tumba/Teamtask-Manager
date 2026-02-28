<?php

namespace App\Factory;

use App\Entity\FriendRequest;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<FriendRequest>
 */
final class FriendRequestFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }



    public static function class(): string
    {
        return FriendRequest::class;
    }



    /**
     * Instanciate a friend request between the sender and the receiver.
     */
    public static function requestFromTo(User $userSender, User $userReceiver): FriendRequestFactory
    {
        return self::new([
            'userSender' => $userSender,
            'userReceiver' => $userReceiver,
        ]);
    }



    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'userReceiver' => UserFactory::new(),
            'userSender' => UserFactory::new(),
        ];
    }



    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(FriendRequest $friendRequest): void {})
        ;
    }
}
