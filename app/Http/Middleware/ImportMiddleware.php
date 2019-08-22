<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class ImportMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $password = $request->password;
        $user_id = $request->user;
        
        if (!$password) {
            return abort(401);
        }

        try {

            $user = User::findOrFail($user_id);
            $isAuth = \Hash::check($password, $user->password);

            if ($isAuth) {
                return $next($request);
            }else {
                return abort(401);
            }

        } catch (\Throwable $th) {

            return abort(404);

        }

    }
}
