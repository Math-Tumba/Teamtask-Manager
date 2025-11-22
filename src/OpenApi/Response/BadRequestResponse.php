<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 400 bad request response attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BadRequestResponse extends OA\Response
{
    public function __construct(string $description = 'Invalid request parameters or format')
    {
        parent::__construct(
            response: JsonResponse::HTTP_BAD_REQUEST,
            description: $description,
        );
    }
}
