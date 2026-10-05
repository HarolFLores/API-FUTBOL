<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Build standard response format
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $extraMeta
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, $message = 'Success', $code = 200, $extraMeta = [])
    {
        return response()->json([
            'success'   => true,
            'message'   => $message,
            'data'      => $data,
            'meta'      => array_merge(['timestamp' => now()->toIso8601String()], $extraMeta),
        ], $code);
    }

    /**
     * Build standard error response
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @param array $extraMeta
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = 'Error', $code = 400, $data = null, $extraMeta = [])
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'data'      => $data,
            'meta'      => array_merge(['timestamp' => now()->toIso8601String()], $extraMeta),
        ], $code);
    }
}
