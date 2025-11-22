<?php

namespace App\OpenApi\JsonRequestBody;

use Attribute;
use OpenApi\Attributes as OA;

/**
 * Reusable request body attribute for single file upload.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class JsonRequestBodyFile extends OA\RequestBody
{
    public function __construct(
        string $propertyName = 'File',
        string $description = 'The file to upload.',
    ) {
        parent::__construct(
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: [$propertyName],
                    properties: [
                        new OA\Property(
                            property: $propertyName,
                            description: $description,
                            type: 'string',
                            format: 'binary'
                        ),
                    ]
                )
            )
        );
    }
}
