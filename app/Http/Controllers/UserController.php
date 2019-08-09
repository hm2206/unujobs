<?php
/**
 * Http/Controllers/UserController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * Class UserController
 * 
 * @category Controllers
 */
class UserController extends Controller
{
    
    /**
     * Muestra un contador de las notificaciones no leídas
     * @return  int
     */
    public function countUnread()
    {
        return auth()->user()->unreadNotifications->count();
    }


    /**
     * Muestra una lista de recursos de las notificaciones no leídas
     * @return  Illuminate\Notifications\DatabaseNotification
     */
    public function unread()
    {   
        $user = auth()->user();
        return $user->unreadNotifications->take(10);
    }

    /**
     * Marca a las notificaciones no leídas como leídas
     * @return Illuminate\Notifications\DatabaseNotification
     */
    public function markAsRead($id) 
    {
        $user = auth()->user();
        foreach ($user->notifications as $notify) {
            if ($notify->id == $id) {
                $notify->markAsRead();
                break;
            }
        }

        return $user->unreadNotifications->take(10);
    }


    public function store(Request $request)
    {

        $this->validate(request(), [
            "ape_paterno" => "required|max:100",
            "ape_materno" => "required|max:100",
            "nombres" => "required|max:100",
            "email" => "required|unique:users",
            "password" => "min:8|max:50"
        ]);

        try {
    
            $roles = $request->input("roles", []);
            $nombre_completo = "{$request->ape_paterno} {$request->ape_materno} {$request->nombres}";
    
            $user = User::create([
                "ape_paterno" => $request->ape_paterno,
                "ape_materno" => $request->ape_materno,
                "nombres" => $request->nombres,
                "email" => $request->email,
                "password" => \bcrypt($request->password),
                "nombre_completo" => $nombre_completo
            ]);

            $user->roles()->syncWithoutDetaching($roles);
    
            return [
                "status" => true,
                "message" => "EL resgistro fué exitoso",
                "body" => $user 
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];

        }

    }


    public function update(Request $request, $id) 
    {
        $payload = $this->validate(request(), [
            "ape_paterno" => "required|max:100",
            "ape_materno" => "required|max:100",
            "nombres" => "required|max:100",
        ]);

        try {
    
            $roles = $request->input("roles", []);
            $nombre_completo = "{$request->ape_paterno} {$request->ape_materno} {$request->nombres}";
    
            $user = User::findOrFail($id);
            $user->update([
                "ape_paterno" => $request->ape_paterno,
                "ape_materno" => $request->ape_materno,
                "nombres" => $request->nombres,
                "nombre_completo" => $nombre_completo
            ]);

            $user->roles()->sync($roles);
    
            return [
                "status" => true,
                "message" => "EL registro fué exitoso",
                "body" => $user 
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];

        }
    }

    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function current()
    {
        try {
            $auth = \auth()->user();
            return [
                "id" => $auth->id,
                "nombre_completo" => $auth->nombre_completo,
                "email" => $auth->email
            ];
        } catch (\Throwable $th) {
            return abort(401);
        }
    }


    public function recovery(Request $request)
    {
        $this->validate(\request(), [
            "email" => "required",
            "password" => "required"
        ]);

        try {

            $user = User::where("email", $request->email)->firsOrFail();

            if (\Hash::check($request->password, $user->password)) {
                auth()->login($user);

                return [
                    "status" => true,
                    "message" => "La sesión fue recuperada correctamente"
                ];
            }

        } catch (\Throwable $th) {
            
            return [
                "status" => false,
                "message" => "Las credenciales son incorrectas"
            ];

        }

    }

}
