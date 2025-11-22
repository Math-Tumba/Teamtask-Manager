<?php

namespace App\OpenApi\Response;

use Attribute;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Reusable 201 success created response attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class SuccessCreatedResponse extends OA\Response
{
    public function __construct(string $modelClass, string $description = 'Resource successfully created.')
    {
        parent::__construct(
            response: JsonResponse::HTTP_CREATED,
            description: $description,
            content: new Model(type: $modelClass)
        );
    }
}
