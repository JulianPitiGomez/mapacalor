<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
        return view('grupos.index');
    }

    public function create()
    {
        return view('grupos.create');
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', compact('grupo'));
    }
}
