<?php

namespace App\OpenApi\Model\Pagination\Response;

use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

/**
 * Reusable 200 success response attribute for paginated items.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class PaginationSuccessResponse extends OA\Response
{
    public function __construct(string $itemClass, string $description = 'Items successfully found.')
    {
        parent::__construct(
            response: 200,
            description: $description,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'items',
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: $itemClass))
                    ),
                    new OA\Property(property: 'total', type: 'integer', example: 94),
                    new OA\Property(property: 'page', type: 'integer', example: 3),
                    new OA\Property(property: 'lastPage', type: 'integer', example: 10),
                ]
            )
        );
    }
}
