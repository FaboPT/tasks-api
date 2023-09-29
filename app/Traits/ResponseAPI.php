<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseAPI
{
    /**
     * Send any success response.
     */
    public function success(string $message = null, int $statusCode = Response::HTTP_OK, object $data = null, string $nameData = 'data'): JsonResponse
    {
        return $this->coreResponse($statusCode, $message, true, $data, $nameData);
    }

    /**
     * Core of response.
     */
    public function coreResponse(int $statusCode, string $message = null, bool $isSuccess = true, object $data = null, string $nameData = 'data'): JsonResponse
    {
        return response()->json($this->responseData($isSuccess, $nameData, $data, $message), $statusCode);
    }

    /**
     * Send any error response.
     */
    public function error(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->coreResponse($statusCode, $message, false);
    }

    /**
     * Method to generate the response data.
     */
    private function responseData(bool $isSuccess, string $nameData, object $data = null, string $message = null): array
    {
        if ($data) {
            return [
                $nameData => $data,
                'success' => $isSuccess,
            ];
        }

        return [
            'message' => $message,
            'success' => $isSuccess,
        ];
    }
}
