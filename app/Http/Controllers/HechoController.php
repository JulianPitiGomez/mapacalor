<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HechoController extends Controller
{
    public function index()
    {
        return view('hechos.index');
    }

    public function create()
    {
        return view('hechos.create');
    }

    public function edit($id)
    {
        return view('hechos.edit', ['hechoId' => $id]);
    }
}
