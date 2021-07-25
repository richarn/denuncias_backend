<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SignupActivate;

class AuthController extends Controller
{

    public function index(Request $request){
        
        $usuarios = User::all();

        return response()->json([
            'success' => true,
            'usuarios' => $usuarios
        ], 200);

    }

    //show 
    public function show(Request $request, $id) {
                        
        $usuarios = User::find($id);

        return response()->json([
            'success' => true,  
            'usuarios' => $usuarios
        ]);
                        
    }
    
    // PUT
    public function update(Request $request, $id) {

        $name = $request->get('name');
        $email = $request->get('email');
        $ci = $request->get('ci');
        $telefono = $request->get('telefono');
        $id_barrio = $request->get('id_barrio');
        $id_role = $request->get('id_role');
        $estado = $request->get('estado');


        $usuarios = User::find($id);


        $usuarios -> name = $name;
        $usuarios -> email = $email;
        $usuarios -> ci = $ci;
        $usuarios -> telefono = $telefono;
        $usuarios -> id_barrio = $id_barrio;
        $usuarios -> id_role = $id_role;
        $usuarios -> estado = $estado ? $estado : $usuarios->estado;
		$usuarios->save();
    
        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);  
    }    
    
    
    
    // DELETE
    public function destroy(Request $request, $id) {

        $res=User::find($id)->delete();
        if ($res){
            $data=[
                'status'=>'1',
                'msg'=>'success'
            ];
            return response()->json(["elimina" => $data]);
        }

        $data=[
            'status'=>'0',
            'msg'=>'fail'
        ];
        return response()->json(["elimina" => $data]);
        
    }

    // Registro
    public function register(Request $request)
    {
        $request->validate([
            'name'   => 'required|string',
            'email'    => 'required|string|email|unique:usuarios',
            'password' => 'required|string|confirmed',
        ]);

        $role = $request->id_role;
        if (!$role) {
            $search = Roles::where('nivel', '=', 2)->first();
            $role = $search->id;
        }

        $user = new User([
            'name'   => $request->name,
            'email'    => $request->email,
            'ci'    => (int) $request->ci,
            'telefono'    => $request->telefono,
            'id_barrio'    => $request->id_barrio,
            'password' => bcrypt($request->password),
            'activation_token'  => Str::random(60),
            'id_role' => $role,
            'estado' => $request->estado
        ]);

        $user->save();

        $tokenResult = $this->createToken($request);
        if (!$tokenResult) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // $user->notify(new SignupActivate($user));

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente!',
            'access_token' => $tokenResult->accessToken
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            // 'remember_me' => 'boolean',
        ]);

        $tokenResult = $this->createToken($request);
        if (!$tokenResult) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = User::find($tokenResult->token->user_id);
        if ($user) {

            return response()->json([
                'success' => true,
                'access_token' => $tokenResult->accessToken,
                'token_type'   => 'Bearer',
                'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['success' => true, 'message' => 'Session finalizada correctamente']);
    }

    public function user(Request $request)
    {
        $user = $request->user()->load('role');

        return response()->json($user);
    }

    // Crear token de sesi贸n
    function createToken(Request $request) {
        $credentials = request(['email', 'password']);
        // $credentials['estado'] = 1;
        // $credentials['deleted_at'] = null;

        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return $tokenResult;
    }

    // Activar el usuario
    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect("/#/auth/activated?status=error");
        }

        $user->estado = 1;
        $user->activation_token = '';
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        return redirect("/#/auth/activated?status=success");
    }

    /**
     * Activated and assign password
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createPassword(Request $request)
    {
        $request->validate([
            'token'   => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where('activation_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'El token no es valido o ha caducado'
            ], 404);
        }

        $user->estado = 1;
        $user->activation_token = '';
        $user->password = bcrypt($request->password);
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario activado correctamente'
        ], 200);
    }



}
