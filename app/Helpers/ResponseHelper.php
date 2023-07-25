<?php

namespace App\Helpers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\ResponseFactory as RoutingResponseFactory;

class ResponseHelper
{
    public static function baseResponse(String $message, $status, $data = null, String $error = null,)
    {
        $response = [
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'error' => $error,
        ];

        return response()->json($response, $status);
    }

    public static function err($message)
    {
        return response()->json([
            "message" => $message,
            'status' => 401,
            "data" => null,
            "error" => null,
        ], 401);
    }
}
