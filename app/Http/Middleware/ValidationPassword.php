<?php
/**
 * Http/Middleware/ValidationPassword.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Middleware;

use Closure;
use \Hash;

/**
 * Se ejecuta antes que los controladores
 * 
 * @category Middleware
 */
class ValidationPassword
{

    /**
     * Valida las credenciales de acceso del usuario autenticado
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return void
     */
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


    /**
     * Muestra un mensaje de error
     *
     * @param string $message
     * @return void
     */
    private function messageError($message = "Credencial de validación errónea!")
    {
        return back()->with(["danger" => $message]);
    }

}
