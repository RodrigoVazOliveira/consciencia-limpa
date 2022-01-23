<?php
namespace App\Controller\Validations;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ErrorExceptions
{
    public static function badRequestBuilder($message): JsonResponse
    {
        return new JsonResponse([
            'erros' => $message,
            'status' => 'BAD_REQUEST',
            'code' => 400
        ], 400);
    }
}