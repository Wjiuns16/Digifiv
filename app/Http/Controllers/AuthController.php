<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 5;
    protected $decayMinutes = 5;

    public function login(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            $message = 'Too many login attempts. Please try again in ' . $seconds . 'seconds.';

            return $this->sendError(null, $message);
        }

        $credentials = $request->only(['email', 'password']);
        if (isset($credentials['email'])) {
            $credentials['email'] = Str::lower($credentials['email']);
        }
        if (!Auth::guard('web')->attempt($credentials)) {
            $this->incrementLoginAttempts($request);

            return $this->sendError(null, 'Email or password incorrect.');
        }

        $user = Auth::guard('web')->user();

        $result = $user->createToken('');
        $token = $result->plainTextToken;

        return $this->sendResponse([
            'user' => $user->data(),
            'token' => [
                'accessToken' => $token,
                'tokenType' => 'Bearer',
            ]], 'User logged in successfully');
    }

    public function username()
    {
        return 'email';
    }

}
