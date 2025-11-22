<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 204 success no content response attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class SuccessNoContentResponse extends OA\Response
{
    public function __construct(string $description = 'Successful operation.')
    {
        parent::__construct(
            response: JsonResponse::HTTP_NO_CONTENT,
            description: $description,
        );
    }
}
