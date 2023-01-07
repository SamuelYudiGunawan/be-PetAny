<?php

namespace App\Http\Controllers;

use App\Models\Petshop;
use App\Http\Requests\StorePetshopRequest;
use App\Http\Requests\UpdatePetshopRequest;

class PetshopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StorePetshopRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePetshopRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Petshop  $petshop
     * @return \Illuminate\Http\Response
     */
    public function show(Petshop $petshop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Petshop  $petshop
     * @return \Illuminate\Http\Response
     */
    public function edit(Petshop $petshop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePetshopRequest  $request
     * @param  \App\Models\Petshop  $petshop
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePetshopRequest $request, Petshop $petshop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Petshop  $petshop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Petshop $petshop)
    {
        //
    }
}
