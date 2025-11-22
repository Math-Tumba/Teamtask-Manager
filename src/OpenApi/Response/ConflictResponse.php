<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 409 conflict response attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ConflictResponse extends OA\Response
{
    public function __construct(string $description = 'Request could not be handled due to a conflict with the resource.')
    {
        parent::__construct(
            response: JsonResponse::HTTP_CONFLICT,
            description: $description,
        );
    }
}
