<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $responseOK = RESPONSE::HTTP_OK;
    public $responseBadRequest = RESPONSE::HTTP_BAD_REQUEST;
    public $responseUnAuthorized = RESPONSE::HTTP_UNAUTHORIZED;
    public $textNotAuthorized = 'Not Authorized';

    /**
     * sends the success response in json format.
     *
     * @param mixed $data
     * @param string $message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccessResponse($data = null, $message = null, $statusCode = 0)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'successData' => $data
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * sends the error response in json format.
     *
     * @param mixed $data
     * @param string $message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendErrorResponse($data = null, $message = null, $statusCode = 0)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'errorData' => $data
        ];

        return response()->json($response, $statusCode);
    }
}
