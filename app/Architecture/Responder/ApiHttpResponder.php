<?php

namespace App\Architecture\Responder;

use Illuminate\Http\JsonResponse;

class ApiHttpResponder implements IApiHttpResponder
{
    public function sendSuccess(array $data = [], int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    public function sendError(string $message = null, int $code = 404): JsonResponse
    {
        if(is_null($message))
        {
            $message = trans('messages.error_occurred');
        }

        return response()->json([
            'message' => $message
        ], $code);
    }
}
