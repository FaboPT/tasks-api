<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseAPI
{
    /**
     * Send any success response
     * @param string|null $message
     * @param int $status_code
     * @param object|null $data
     * @param string|null $name_data
     * @return JsonResponse
     */
    public function success(string $message = null, int $status_code = Response::HTTP_OK, object $data = null, string $name_data = 'data'): JsonResponse
    {
        return $this->core_response($status_code, $message, true, $data, $name_data);
    }

    /**
     * Core of response
     *
     * @param int $status_code
     * @param string|null $message
     * @param bool $is_success
     * @param object|null $data
     * @param string $name_data
     * @return JsonResponse
     */
    public function core_response(int $status_code, string $message = null, bool $is_success = true, object $data = null, string $name_data = 'data'): JsonResponse
    {
        return response()->json($this->response_data($is_success, $name_data, $data, $message), $status_code);
    }

    /**
     * Method to generate the response data
     * @param bool $is_success
     * @param object|null $data
     * @param string|null $message
     * @param string $name_data
     * @return array
     */
    private function response_data(bool $is_success, string $name_data, object $data = null, string $message = null): array
    {
        if ($data) {
            return [
                $name_data => $data,
                'success' => $is_success
            ];
        }
        return [
            'message' => $message,
            'success' => $is_success
        ];
    }

    /**
     * Send any error response
     * @param string $message
     * @param int $status_code
     * @return JsonResponse
     */
    public function error(string $message, int $status_code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->core_response($status_code, $message, false);
    }
}
