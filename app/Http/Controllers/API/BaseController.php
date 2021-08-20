<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

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

    public function respond($data, $msg = null) {
        return ResponseBuilder::asSuccess()->withData($data)->withMessage($msg)->build();
    }

    public function respondWithMessage($msg) {
        return ResponseBuilder::asSuccess()->withMessage($msg)->build();
    }

    public function respondWithError($api_code, $http_code) {
        return ResponseBuilder::asError($api_code)->withHttpCode($http_code)->build();
    }

    public function respondBadRequest($api_code) {
        return $this->respondWithError($api_code, 400);
    }
    public function respondUnAuthorizedRequest($api_code) {
        return $this->respondWithError($api_code, 401);
    }
    public function respondNotFound($api_code) {
        return $this->respondWithError($api_code, 404);
    }
}
