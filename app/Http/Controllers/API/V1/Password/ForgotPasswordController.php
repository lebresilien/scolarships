<?php

namespace App\Http\Controllers\API\V1\Password;

use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;
use App\ApiCode;
use App\Models\User;

class ForgotPasswordController extends BaseController
{
    public function forgot() 
    {
        $credentials = request()->validate(['email' => 'required|email']);

        $user = User::where('email', $credentials['email'])->first();
        if(!$user) return $this->respondWithMessage('User not found');

        Password::sendResetLink($credentials);
        return $this->respondWithMessage('Reset password link sent on your email id.');
    }


    public function reset(ResetPasswordRequest $request) 
    {
        $reset_password_status = Password::reset($request->validated(), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->respondBadRequest(ApiCode::INVALID_RESET_PASSWORD_TOKEN);
        }

        return $this->respondWithMessage("Password has been successfully changed");
    }
}
