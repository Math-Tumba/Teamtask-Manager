<?php

namespace App\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 200 success response attribute.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class SuccessResponse extends OA\Response
{
    
    public function __construct(string $modelClass, string $description = 'Successful operation.')
    {
        parent::__construct(
            response: JsonResponse::HTTP_OK,
            description: $description,
            content: new Model(type: $modelClass)
        );
    }
}