<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Auditoria;

class AuditoriaMiddleware
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
        $user = auth()->user();
        $user_id = 0;

        if ($user) {
            $user_id = $user->id;
        }

        $body =  json_encode(array_slice(func_get_args(), 2));
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        Auditoria::create([
            "ip" => $ip,
            "body" => $body,
            "user_id" => $user_id,
            "client_agent" => $user_agent,
            "user_id" => $user_id
        ]);

        return $next($request);
    }

}
