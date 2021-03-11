<?php
/**
 * Http/Controllers/PlanillaController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Planilla;
use Illuminate\Http\Request;

/**
 * Class PlanillaController
 * 
 * @category Controllers
 */
class PlanillaController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectRedirect
     */
    public function create()
    {
        return back();
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Planilla $planilla)
    {
        return back();
    }

    /**
     * @param  \App\Planilla  $planilla
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(Planilla $planilla)
    {
        return back();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Planilla  $planilla
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Planilla $planilla)
    {
        return back();
    }

    /**
     * @param  \App\Models\Planilla  $planilla
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Planilla $planilla)
    {
        return back();
    }
}
