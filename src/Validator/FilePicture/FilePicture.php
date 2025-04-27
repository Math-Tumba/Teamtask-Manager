<?php

namespace App\Validator\FilePicture;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class FilePicture extends Constraint
{
    public string $messageAllowedMimesTypes = "Le format du fichier est invalide. (Formats autorisés : {{ allowedMimeTypes }}).";
    public string $messageMaxSize = "La taille du fichier est trop élevée et doit être inférieure à {{ limit }}.";

    // You can use #[HasNamedArguments] to make some constraint options required.
    // All configurable options must be passed to the constructor.
    public function __construct(
        public string $mode = 'strict',
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}