<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\ImagenDenuncias;
use App\Models\Denuncias;
use App\Models\Imagenes;
use Carbon\Carbon;

class DenunciasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    // GET
    public function index(Request $request){

        $query = Denuncias::with('barrio', 'imagenes'); // with('relacion del modelo')

        $barrio = $request->get('id_barrio');
        if ($barrio) {
            $query->where('id_barrio', '=', $barrio);
        }

        $estado = $request->get('estado');
        if ($estado) {
            $query->where('estado', '=', $estado);
        }

        $usuario = $request->get('usuario');
        if ($usuario) {
            $query->where('id_user', '=', $usuario);
        }

        $fecha = $request->get('fecha');
        if ($fecha) {
            // Fecha seleccionada
            // $dt = date("Y-m-d", strtotime($fecha));
            $dt = new Carbon($fecha);
            
            // Le agrega 1 dia para filtrar solo en el dia seleccionado
            $tomorrow = new Carbon($fecha);
            $tomorrow->addDays(1);

            $query->where('created_at', '>=', $dt->format("Y-m-d"))
            ->where('created_at', '<', $tomorrow->format("Y-m-d")); // formatear fecha
            //format('d-m-Y')
            
        }

        $denuncias = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $denuncias
        ], 200);

    }

            // GET/ID
    public function show($id) {

        // no probaste asi ??
        $denuncia = Denuncias::with('imagenes', 'denunciante', 'barrio')->find($id);

        return response()->json([
            'success' => true,  
            'data' => $denuncia
        ]);
    }


    // POST
    public function store(Request $request) {

        $fecha_solucion = $request->get('fecha_solucion');
        $descripcion_denuncia = $request->get('descripcion_denuncia');
        $ubicacion = $request->get('ubicacion');
        $id_barrio = $request->get('id_barrio');
        $id_user = $request->get('id_user');

        $denuncias = new Denuncias();

        // return response()->json(['lat' => $lat, 'lng' => $lng]);

        $denuncias -> fecha_denuncia = Carbon::now();
        $denuncias -> fecha_solucion = $fecha_solucion;
        $denuncias -> ubicacion = $ubicacion;
        $denuncias -> descripcion_denuncia = $descripcion_denuncia;
        $denuncias -> id_barrio = $id_barrio;
        $denuncias -> id_user = $id_user;
        
        if ($denuncias -> save()) {


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
                    $path = 'storage/api/'.$id_user;
                    $data->move($path, $nombreImagen);

                    $imagen = new Imagenes();
                    $imagen->url = $path.'/'.$nombreImagen;

                    if ($imagen->save()) {
                        $aux = new ImagenDenuncias();
                        $aux->id_imagen = $imagen->id;
                        $aux->id_denuncia = $denuncias->id;

                        $aux->save();
                    }
                }

            }

            return response()->json(['success' => true, 'message' => 'Denuncia registrada correctamente']);
        }    

        return response()->json(['success' => false, 'message' => 'Ha ocurrido un problema al intentar guardar la denuncia']);
    }
    
    // PUT
    public function update(Request $request, $id) {
        $fecha_solucion = $request->get('fecha_solucion');
        $descripcion_denuncia = $request->get('descripcion_denuncia');
        $ubicacion = $request->get('ubicacion');
        $id_barrio = $request->get('id_barrio');
        $id_user = $request->get('id_user');
        $estado = $request->get('estado');

        $denuncias = Denuncias::find($id);

        // return response()->json(['lat' => $lat, 'lng' => $lng]);

        $denuncias -> fecha_denuncia = Carbon::now();
        // $denuncias -> fecha_solucion = $fecha_solucion;
        $denuncias -> ubicacion = $ubicacion;
        $denuncias -> descripcion_denuncia = $descripcion_denuncia;
        $denuncias -> id_barrio = $id_barrio;
        $denuncias -> id_user = $id_user;
        $denuncias -> estado = $estado ? $estado : $denuncias->estado;

        // actualizar campos al confirmar la denuncia
        if ($denuncias->estado == 1) $denuncias->fecha_solucion = Carbon::now();
        
        // obtener y actualizar estado de las imagenes relacionadas

        if ($denuncias -> save()) {


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
                    $path = 'storage/api/'.$id_user;
                    $data->move($path, $nombreImagen);

                    $imagen = new Imagenes();
                    $imagen->estado = $denuncias->estado;
                    $imagen->url = $path.'/'.$nombreImagen;

                    if ($imagen->save()) {
                        $aux = new ImagenDenuncias();
                        $aux->id_imagen = $imagen->id;
                        $aux->id_denuncia = $denuncias->id;

                        $aux->save();
                    }
                }

            }

            return response()->json(['success' => true, 'message' => 'Denuncia actualizada correctamente']);
        }    
    }

    // DELETE
    public function destroy(Request $request, $id) {

        $idImagenes = ImagenDenuncias::where('id_denuncia', '=', $id)->pluck('id_imagen');
        ImagenDenuncias::where('id_denuncia', '=', $id)->delete();
        Imagenes::whereIn('id', $idImagenes)->delete();
        $res=Denuncias::find($id)->delete();
        
        if ($res){
            $data=[
                'status'=>'1',
                'msg'=>'success'
            ];

            return response()->json("elimina",$data);
        }

        $data=[
            'status'=>'0',
            'msg'=>'fail'
        ];
        return response()->json("elimina",$data);
    
    }

    public function removeImage($idDenuncia, $idImagen) {
        $imagen = ImagenDenuncias::where('id_imagen', '=', $idImagen);

        if ($imagen && $imagen->delete()) {
            Imagenes::find($idImagen)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se encontro la imagen solicitada'
        ]);
    }

}
