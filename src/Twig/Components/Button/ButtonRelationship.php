<?php

namespace App\Twig\Components\Button;

use App\Service\Users\RelationshipService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: '_button_relationship', template: '_components/button/_button_relationship.html.twig')]
final class ButtonRelationship
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $userId;

    public function __construct(
        private RelationshipService $relationshipService,
    ) {
    }

    public function getRelationshipState()
    {
        return $this->relationshipService->getState($this->userId);
    }
}
