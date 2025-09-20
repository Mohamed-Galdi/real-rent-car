<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $default = '/';
        if ($user) {
            $default = match ($user->role) {
                UserRole::ADMIN => route('admin.cars.index'),
                UserRole::CLIENT => route('client.reservations.index'),
                default => '/',
            };
        }

        return redirect()->intended($default);
    }
}
