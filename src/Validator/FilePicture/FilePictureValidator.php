<?php

namespace App\Validator\FilePicture;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class FilePictureValidator extends ConstraintValidator
{
    private const MAX_SIZE_MO = 3;
    private const MAX_SIZE_BYTES = self::MAX_SIZE_MO * 1024 * 1024;

    /**
     * Validate uploaded files based on FilePicture constraint.
     *
     * @throws HttpException if the function is called with a different constraint type than FilePicture
     *                       if $value is not an UploadedFile
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FilePicture) {
            throw new UnexpectedTypeException($constraint, FilePicture::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof UploadedFile) {
            throw new UnexpectedValueException($constraint, UploadedFile::class);
        }

        $mimeTypes = [
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];
        if (!in_array($value->getMimeType(), $mimeTypes)) {
            $this->context->buildViolation($constraint->messageAllowedMimesTypes)
                ->setParameter('{{ allowedMimeTypes }}', implode(', ', array_keys($mimeTypes)))
                ->addViolation();
        }

        if ($value->getSize() > self::MAX_SIZE_BYTES) {
            $this->context->buildViolation($constraint->messageMaxSize)
                ->setParameter('{{ limit }}', self::MAX_SIZE_MO)
                ->addViolation();
        }
    }
}
