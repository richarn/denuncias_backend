<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request){

        $roles = Roles::all();

        return response()->json([
            'success' => true,
            'roles' => $roles
        ], 200);

    }

    // POST
    public function store() {}
    
    // PUT
    public function update() {}

    // DELETE
    public function delete() {}
}
