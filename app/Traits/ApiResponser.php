<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
    /**
     * Build successfull response.
     * @param string|array $data
     * @param int $code
     * @param Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $code = Response::HTTP_OK)
    {
        return response($data, $code)->header('Content-Type', 'application/json');   
    }

    /**
     * Build error response.
     * @param string|array $message
     * @param int $code
     * @param Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'error_code' => $code], $code);
    }

    /**
     * Build error message.
     * @param string|array $message
     * @param int $code
     * @param Illuminate\Http\JsonResponse
     */
    public function errorMessage($message, $code)
    {
        return response($message, $code)->header('Content-Type', 'application/json');
    }
}
