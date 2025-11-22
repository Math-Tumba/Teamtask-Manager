<?php

namespace App\OpenApi\Response;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reusable 422 Unprocessable Entity response for validation errors.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class ValidationErrorResponse extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: Response::HTTP_UNPROCESSABLE_ENTITY,
            description: 'Validation errors in request data.',
        );
    }
}
