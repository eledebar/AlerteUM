<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index()
    {
        return view('admin.incidents.index');
    }

    // Puedes añadir más métodos como create, store, edit, etc.
}
