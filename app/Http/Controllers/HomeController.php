<?php
/**
 * Http/Controllers/HomeController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Info;
use App\Models\Postulante;

/**
 * Class HomeController
 * 
 * @category Controllers
 */
class HomeController extends Controller
{

    /**
     * Constructor
     * 
     * @return void
     */
    function __construct() {
        $this->middleware("auth"); 
    }

    /**
     * Muestra una vista de bienvenidad
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $infos = Info::where('active', 1)->get()->count();
        $postulantes = Postulante::get()->count();
        return view('home', \compact('infos', 'postulantes'));
    }

    /**
     * Muestra una vista de las planillas
     *
     * @return \Illuminate\View\View
     */
    public function planilla() 
    {
        return view("planilla");
    }
    
}
