<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * User Registration api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(
                $validator->errors(),
                config('constants.USER_REGISTERED_UNSUCCESS_MSG'),
                $this->responseBadRequest
            );
        }

        $input = $request->all();

        $input['name'] = trim($input['name']);
        $input['email'] = trim($input['email']);
        $input['password'] = trim(bcrypt($input['password']));

        $user = User::create($input);

        return $this->sendSuccessResponse(
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->createToken('DemoBlog')->plainTextToken,
                'registered_at' => $user->created_at->format('d-m-Y h:i:s')
            ],
            config('constants.USER_REGISTERED_SUCCESS_MSG'),
            $this->responseOK
        );
    }

    /**
     * User Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $message = config('constants.USER_LOGGED_IN_SUCCESS_MSG');
            if (Auth::check()) {
                dd($user->currentAccessToken());
                dd(Auth::check());
                $token = $user->tokens()->where('name', 'auth_token')->first();
                dd($token);

                $token = $token ? $token->plainTextToken : $user->createToken('DemoBlog')->plainTextToken;

                $message = config('constants.USER_ALREADY_LOGGED_IN_MSG');
            }
            // $token = Auth::check() ? $user->tokens()->where('name', 'auth_token')->first()->plainTextToken
            //     :
            //     $user->createToken('DemoBlog')->plainTextToken;
            // $message = Auth::check() ? config('constants.USER_ALREADY_LOGGED_IN_MSG')
            //     :
            //     config('constants.USER_LOGGED_IN_SUCCESS_MSG');

            return $this->sendSuccessResponse(
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token,
                    'logged_at' => date('d-m-Y H:i:s')
                ],
                $message,
                $this->responseOK
            );
        } else {
            return $this->sendErrorResponse(
                [
                    'error' => $this->textNotAuthorized
                ],
                config('constants.NOT_AUTHORIZED_ERR_MSG'),
                $this->responseBadRequest
            );
        }
    }

    /**
     * User Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendSuccessResponse(
            [],
            config('constants.USER_LOGGED_OUT_SUCCESS_MSG'),
            $this->responseOK
        );
    }
}
