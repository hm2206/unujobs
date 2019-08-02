<?php

namespace App\Http\Controllers;

use App\Models\Afp;
use Illuminate\Http\Request;

class AfpController extends Controller
{

    public function index()
    {
        $afps = Afp::all();

        return view('afps.index', compact('afps'));
    }

    
    public function create()
    {
        return view('afps.create');
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            "nombre" => "required|unique:afps",
            "flujo" => "required|numeric",
            "mixta" => "required|numeric",
            "aporte" => "required|numeric",
            "prima" => "required|numeric"
        ]);

        $afp = Afp::create($request->all());

        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }


    public function show(Afp $afp)
    {
        return back();
    }


    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $afp = Afp::findOrFail($id);

        return view('afps.edit', compact('afp'));
    }


    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "nombre" => "required",
            "flujo" => "required|numeric",
            "mixta" => "required|numeric",
            "aporte" => "required|numeric",
            "prima" => "required|numeric"
        ]);

        $afp = Afp::findOrFail($id);
        $afp->update($request->all());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Afp  $afp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Afp $afp)
    {
        return back();
    }
}
