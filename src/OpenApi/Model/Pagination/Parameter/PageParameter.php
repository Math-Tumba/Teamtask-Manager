<?php

namespace App\OpenApi\Model\Pagination\Parameter;

use OpenApi\Attributes as OA;

/**
 * Reusable pagination page number parameter.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class PageParameter extends OA\Parameter
{
    public function __construct()
    {
        parent::__construct(
            name: 'page',
            in: 'query',
            schema: new OA\Schema(
                type: 'int',
                description: 'Page number for pagination (1-indexed)',
            )
        );
    }
}
