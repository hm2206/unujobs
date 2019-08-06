<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Work;
use App\Models\Postulante;

class HomeController extends Controller
{

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
        $works = Work::get()->count();
        $postulantes = Postulante::get()->count();
        return view('home', \compact('works', 'postulantes'));
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
