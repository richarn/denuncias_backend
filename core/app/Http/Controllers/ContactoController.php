<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
        // GET
        public function index(){

        }
    
    
        // GET/ID
        public function show() {

        }
    
    
        // POST
        public function store(Request $request){
    
            $nombre = $request->get('nombre');
            $descripcion = $request->get('descripcion');
    
            $contacto = new Contacto();
    
            $contacto -> nombre = $nombre;
            $contacto -> descripcion = $descripcion;
            $contacto -> save();
         
            return response()->json(['success' => true, 'message' => 'Contacto registrado correctamente']);
        
        } 
}
