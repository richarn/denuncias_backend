<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\ImagenNoticias;
use App\Models\Noticias;
use App\Models\Imagenes;
use Carbon\Carbon;

class NoticiasController extends Controller
{


    // GET
    public function index(Request $request){

        $query = Noticias::with('imagenes'); // with('relacion del modelo')

        $estado = $request->get('estado');
        if ($estado) {
            $query->where('estado', '=', $estado);
        }

        $noticias = $query->get();

        return response()->json([
            'success' => true,  
            'data' => $noticias
        ], 200);
    }


    // GET/ID
    public function show($id) {

        // no probaste asi ??
        $noticia = Noticias::with('imagenes')->find($id);

        return response()->json([
            'success' => true,  
            'data' => $noticia
        ]);
    }


    // POST
    public function store(Request $request){

        $titulo = $request->get('titulo_noticia');
        $descripcion = $request->get('descripcion_noticia');
        $id_user = $request->get('id_user');

        $noticias = new Noticias();

        $noticias -> fecha = Carbon::now();
        $noticias -> titulo = $titulo;
        $noticias -> descripcion = $descripcion;
        
        if ($noticias -> save()) {


            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                
            //     request()->validate([
            //         'imagenes' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            //     ]);

                $imagenes = is_array($imagenes) ? $imagenes : [$imagenes];

                foreach ($imagenes as $data) {
                    $randomSring = Str::random(20);
                    $extension = $data->extension();
                    $nombreImagen = $randomSring.'.'.$extension;
                    $path = 'storage/noticias/'.$noticias->id;
                    $data->move($path, $nombreImagen);

                    $imagen = new Imagenes();
                    $imagen->url = $path.'/'.$nombreImagen;

                    if ($imagen->save()) {
                        $aux = new ImagenNoticias();
                        $aux->id_imagen = $imagen->id;
                        $aux->id_noticia = $noticias->id;

                        $aux->save();
                    }
                }

            }

            return response()->json(['success' => true, 'message' => 'Noticia registrada correctamente']);
        }    

        return response()->json(['success' => false, 'message' => 'Ha ocurrido un problema al intentar guardar la noticia']);

    }   
}
