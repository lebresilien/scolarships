<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;


class BaseController extends Controller
{    
    public function sendResponse($result, $message)
    {
        if (isset($result['data'])) {
            $response = [
                'success' => true,
                'message' => $message
            ];
            $response = array_merge($result, $response);
        } else {
            $response = [
                'success' => true,
                'data' => $result,
                'message' => $message
            ];
        }
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
}
