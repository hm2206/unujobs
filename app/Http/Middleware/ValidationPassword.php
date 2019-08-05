<?php

namespace App\Http\Middleware;

use Closure;
use \Hash;

class ValidationPassword
{

    public function handle($request, Closure $next)
    {
        $password = $request->password;

        if ($password) {

            $auth = auth()->user();
            $verificacion = Hash::check($password, $auth->password);

            if ($verificacion) {
                return $next($request);
            }
            
        }

        return $this->messageError();
    }


    private function messageError($message = "Credencial de validación errónea!")
    {
        return back()->with(["danger" => $message]);
    }

}
