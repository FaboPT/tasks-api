<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseAPI
{
    /**
     * Send any success response
     * @param string|null $message
     * @param int $statusCode
     * @param object|null $data
     * @param string $nameData
     * @return JsonResponse
     */
    public function success(string $message = null, int $statusCode = Response::HTTP_OK, object $data = null, string $nameData = 'data'): JsonResponse
    {
        return $this->coreResponse($statusCode, $message, true, $data, $nameData);
    }

    /**
     * Core of response
     *
     * @param int $statusCode
     * @param string|null $message
     * @param bool $isSuccess
     * @param object|null $data
     * @param string $nameData
     * @return JsonResponse
     */
    public function coreResponse(int $statusCode, string $message = null, bool $isSuccess = true, object $data = null, string $nameData = 'data'): JsonResponse
    {
        return response()->json($this->responseData($isSuccess, $nameData, $data, $message), $statusCode);
    }

    /**
     * Method to generate the response data
     * @param bool $isSuccess
     * @param object|null $data
     * @param string|null $message
     * @param string $nameData
     * @return array
     */
    private function responseData(bool $isSuccess, string $nameData, object $data = null, string $message = null): array
    {
        if ($data) {
            return [
                $nameData => $data,
                'success' => $isSuccess
            ];
        }
        return [
            'message' => $message,
            'success' => $isSuccess
        ];
    }

    /**
     * Send any error response
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->coreResponse($statusCode, $message, false);
    }
}
