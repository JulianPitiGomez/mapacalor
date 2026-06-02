<?php

namespace App\Http\Controllers;

use App\Models\Operativo;
use Illuminate\Http\Request;

class OperativoController extends Controller
{
    public function index()
    {
        return view('operativos.index');
    }

    public function create()
    {
        return view('operativos.create');
    }

    public function edit(Operativo $operativo)
    {
        return view('operativos.edit', compact('operativo'));
    }
}
