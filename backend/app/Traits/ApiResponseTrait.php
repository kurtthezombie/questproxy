<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse(string $message, int $statuscode, $additionalData = [])
    {
        $response = [
            'message' => $message,
            'status' => true,
        ];

        $response = array_merge($response, $additionalData);

        return response()->json($response,$statuscode);
    }

    /**
     * Return a success response ($message,$statuscode)
     */
    protected function failedResponse(string $message, int $statuscode, $additionalData = [])
    {
        $response = [
            'message' => $message,
            'status' => false,
        ];

        $response = array_merge($response, $additionalData);

        return response()->json($response ,$statuscode);
    }
}
