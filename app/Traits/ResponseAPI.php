<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseAPI
{
    /**
     * Core of response
     *
     * @param int $statusCode
     * @param string|null $message
     * @param bool $isSuccess
     * @param object|array|null $data
     * @return JsonResponse
     */
    public function core_response(int $statusCode, string $message = null, bool $isSuccess = true, object|array $data = null): JsonResponse
    {
        if ($isSuccess) {
            return response()->json($this->response_data($isSuccess, $data, $message), $statusCode);
        } else {
            return response()->json([
                'message' => $message,
                'success' => false,
            ], $statusCode);
        }
    }

    /**
     * Send any success response
     * @param string|null $message
     * @param int $statusCode
     * @param object|array|null $data
     * @return JsonResponse
     */
    public function success(string $message = null, int $statusCode = 200, object|array $data = null): JsonResponse
    {
        return $this->core_response($statusCode, $message, true, $data);
    }

    /**
     * Send any error response
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(string $message, int $statusCode = 200): JsonResponse
    {
        return $this->core_response($statusCode, $message, false);
    }

    /**
     * Method to generate the response data
     * @param object|array $data
     * @param string $message
     * @param bool $success
     * @return array
     */
    private function response_data(bool $success, object|array $data = null, string $message = null,): array
    {
        if ($data) {
            return [
                'data' => $data,
                'success' => $success
            ];
        }
        return [
            'message' => $message,
            'success' => $success
        ];
    }
}

