<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Builds the response a preConfirmUrl() endpoint returns. Requires symfony/validator.
 */
final class PreConfirmResponse
{
    /**
     * 422 {"message": first violation} keeps the modal open; otherwise 200 with $value as result.value,
     * or 204 (keeps the user's input) when $value is null.
     */
    public static function fromViolations(ConstraintViolationListInterface $violations, mixed $value = null): Response
    {
        if (\count($violations) > 0) {
            return new JsonResponse(['message' => (string) $violations->get(0)->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // JsonResponse turns null into {}, so a null value answers without a body instead.
        return null === $value ? new Response(status: Response::HTTP_NO_CONTENT) : new JsonResponse($value);
    }
}
