<?php

namespace App\Http\Controllers\API;

use App\Architecture\Responder\IApiHttpResponder;
use App\Http\Requests\API\Auth\Login;
use App\Http\Requests\API\Auth\Register;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    /**
     * @param IApiHttpResponder $apiHttpResponder
     */
    public function __construct(
        public IApiHttpResponder $apiHttpResponder
    )
    {}

    /**
     * @param Login $request
     * @return JsonResponse
     */
    public function login(Login $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authUser = Auth::user();
            $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name'] =  $authUser->name;
            $success['message'] = 'User signed in';

            return $this->apiHttpResponder->sendSuccess($success);
        } else {
            return $this->apiHttpResponder->sendError('Unauthorised', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Register $request
     * @return JsonResponse
     */
    public function register(Register $request): JsonResponse
    {
        $input = $request->except('confirm_password');
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['message'] = 'User created successfully.';

        return $this->apiHttpResponder->sendSuccess($success, Response::HTTP_CREATED);
    }

    public function logout(): JsonResponse
    {
        if(auth()->check())
        {
            auth()->user()->tokens()->delete();
        }
        $success['message'] = 'User Logout successfully.';
        return $this->apiHttpResponder->sendSuccess($success);
    }
}
