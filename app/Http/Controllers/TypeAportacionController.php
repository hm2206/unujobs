<?php

namespace App\Http\Controllers;

use App\Models\TypeAportacion;
use Illuminate\Http\Request;

class TypeAportacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TypeAportacion::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TypeAportacion  $typeAportacion
     * @return \Illuminate\Http\Response
     */
    public function show(TypeAportacion $typeAportacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeAportacion  $typeAportacion
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeAportacion $typeAportacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeAportacion  $typeAportacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TypeAportacion $typeAportacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeAportacion  $typeAportacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeAportacion $typeAportacion)
    {
        //
    }
}
