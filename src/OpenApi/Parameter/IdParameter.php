<?php

namespace App\OpenApi\Parameter;

use OpenApi\Attributes as OA;

/**
 * Reusable id path parameter.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class IdParameter extends OA\Parameter
{
    public function __construct(string $description = 'Resource identifier')
    {
        parent::__construct(
            name: 'id',
            in: 'path',
            description: $description,
            schema: new OA\Schema(
                type: 'int',
                minimum: 1,
                maximum: PHP_INT_MAX
            )
        );
    }
}
