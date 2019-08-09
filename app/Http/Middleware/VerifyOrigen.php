<?php

namespace App\Http\Middleware;

use Closure;
use \App\Models\Modulo;
use \App\Models\Role;

class VerifyOrigen
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
        try {

            $current = auth()->user();

            if ($current->roles->count() > 0) {

                $url = url()->current();

                $posible = Modulo::where("ruta", "like", "%{$url}%")->first();

                if ($posible) {

                    $padre = $posible->parent;
                    $tmp_roles = $padre->roles->pluck("id");
                    $validate = $current->roles->whereIn("id", $tmp_roles)->count();

                    if ($validate > 0) {
        
                        return $next($request);
        
                    }

                }
                
                return redirect("/")->with(["warning" => "Usted no está autorizado para ingresar a está pagina"]);
            }

            return $next($request);

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return redirect("/")->with(["warning" => "Usted no está autorizado para ingresar a está pagina"]);

        }

    }
}
