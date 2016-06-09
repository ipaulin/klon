<?php
namespace Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    protected $validation_messages =  [
        'min' => 'The :attribute must have at least :min characters',
        'max' => 'The :attribute can have max :max characters',
        'password_confirm.same' => 'The :attribute must match password field',
        'required' => 'The :attribute is required',
        'email' => 'The :attribute must be email address',
        'mimes' => 'The :attribute must be jpeg of png format',
    ];


    /**
     * Create new error response
     * @param string $message
     * @param array $additional Additional results
     * @return Response
     */
    protected function errorResponse($message = '', array $additional = [])
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if($additional) {
            foreach($additional as $key => $val) {
                $response[$key] = $val;
            }
        }

        return new Response($response);
    }

    /**
     * Create new success response
     * @param string $message
     * @param array $additional Additional results
     * @return Response
     */
    protected function successResponse($message = '', array $additional = [])
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if($additional) {
            foreach($additional as $key => $val) {
                $response[$key] = $val;
            }
        }

        return new Response($response);
    }
}