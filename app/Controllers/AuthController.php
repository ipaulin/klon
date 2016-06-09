<?php
namespace Controllers;

use Tx\Validator;
use Illuminate\Http\Request;
use Twt\TwtSentinelBootstrapper;


class AuthController extends BaseController
{
    private $sentinel;


    public function __construct()
    {
        $this->sentinel = (new TwtSentinelBootstrapper())->createSentinel();
    }


    /**
     * Login user into system
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function loginAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|max:25',
            'password' => 'required'
        ], $this->validation_messages);

        if($validator->fails()) {
            return $this->errorResponse($validator->errors(), ['user' => null]);
        }

        $credentials = [
            'nickname'    => $request->input('nickname'),
            'password' => $request->input('password'),
        ];

        // if auth fails return error response
        if(!$this->sentinel->authenticate($credentials)) {
            return $this->errorResponse('Username or password incorrect', ['user' => null]);
        }

        $user = $this->sentinel->getUser()->getBigUserObject();

        return $this->successResponse('Logged in', ['user' => $user]);
    }


    /**
     * Register new user in system
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function registerAction(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:100',
            'nickname' => 'required|string|max:25',
            'email' => 'required|email',
            'password' => 'required|min:6|max:15',
            'password_confirm' => 'required|same:password',
            'terms' => 'required|accepted',
        ], $this->validation_messages);

        // check if fields pass criteria
        if($validator->fails()) {
            return $this->errorResponse($validator->errors());
        }

        $credentials = [
            'nickname' => $request->input('nickname'),
            'display_name'    => $request->input('display_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];

        // try to register user and activate profile
        $user = $this->sentinel->register($credentials, true);

        if(!$user) {
            return $this->errorResponse('An error occurred please try again');
        }

        return $this->successResponse('Successfully registered');
    }


    /**
     * Logout
     */
    public function logoutAction()
    {
        $this->sentinel->logout();
    }
}