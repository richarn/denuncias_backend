<?php

namespace App\Http\Controllers;

use App\Models\Barrios;
use Illuminate\Http\Request;

class BarriosController extends Controller
{

    // GET
    public function index(Request $request){

        $barrios = Barrios::all();

        return response()->json([
            'success' => true,
            'barrios' => $barrios
        ], 200);

    }

    // POST
    public function store() {}
    
    // PUT
    public function update() {}

    // DELETE
    public function delete() {}
    
}
