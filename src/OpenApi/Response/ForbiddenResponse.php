<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 403 forbidden response attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class ForbiddenResponse extends OA\Response
{
    public function __construct(string $description = 'Not allowed to perform this action.')
    {
        parent::__construct(
            response: JsonResponse::HTTP_FORBIDDEN,
            description: $description,
        );
    }
}
