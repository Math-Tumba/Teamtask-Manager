<?php

namespace App\OpenApi\JsonRequestBody;

use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

/**
 * Reusable request body attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class JsonRequestBody extends OA\RequestBody
{
    public function __construct(
        string $modelClass,
        string $description = 'Request payload',
        bool $required = true,
    ) {
        parent::__construct(
            required: $required,
            description: $description,
            content: new Model(type: $modelClass)
        );
    }
}
