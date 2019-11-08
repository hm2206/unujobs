<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Hash;
use App\Models\User;
use App\Models\Token;
use App\Models\Modulo;

class AuthController extends Controller
{
    
    /**
     * Iniciar sesión y generar token
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request) {
        $this->validate(request(), [
            "email" => "email|max:100",
            "password" => "max:100|min:6"
        ]);
        // obtener usuario
        $user = User::where('email', $request->email)->firstOrFail();
        // verificamos que la contraseña sea incorrecta
        if (!Hash::check($request->password, $user->password)) {
            return [
                "success" => false,
                "code" => "401",
                "message" => "La contraseña es incorrecta!",
                "token" => ""
            ];
        }
        // crear token
        $newToken = \bcrypt(now());
        // crear sesión
        $token = Token::create([
            "type" => "bearer",
            "token" => $newToken,
            "user_id" => $user->id
        ]);
        // devolver token
        return [
            "success" => true,
            "code" => "201",
            "message" => "La sesión se creo correctamente!",
            "token" => $token->token
        ];
        
    }

    /**
     * cerrar sesión
     *
     * @return void
     */
    public function logout() {
        try {
            $token = User::auth()['token'];
            $token->update(['is_revoked' => 1]);
            return [
                "success" => true,
                "message" => "La sesión se cerró correctamente!"
            ];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => "No se pudó cerrar la sesión!"
            ];
        }
    }


    /**
     * Obtener usuario authenticado!
     *
     * @return void
     */
    public function me()
    {
        return User::auth();
    }


    public function profile()
    {
        $user = User::auth()['user'];
        $profiles = \collect();
        if ($user->modulos->count() > 0) {

            $modulos = $user->modulos->where("modulo_id", null);

            foreach ($modulos as $mod) {
                    
                $mod->modulos = Modulo::whereHas("users", function($u) use($user) {
                    $u->where("users.id", $user->id);
                })->where("modulo_id", $mod->id)
                ->get();

                $profiles->push($mod);
    
            }

        }else {

            $modulos = Modulo::where("modulo_id", null)->get();

            foreach ($modulos as $mod) {
                
                $mod->modulos;
                $profiles->push($mod);

            }

        }   

        return $profiles;
    }

}
