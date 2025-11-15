<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 404 not found response attribute.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NotFoundResponse extends OA\Response
{

    public function __construct(string $resource = 'Resource')
    {
        parent::__construct(
            response: JsonResponse::HTTP_NOT_FOUND,
            description: $resource . ' not found.',
        );
    }
}