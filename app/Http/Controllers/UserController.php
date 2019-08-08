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
