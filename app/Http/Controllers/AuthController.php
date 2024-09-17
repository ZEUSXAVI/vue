<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //

    public function login(Request $request){
        //login

        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(!Auth::attempt($credenciales)){
            return response()->json(["message" => "Credenciales Invalidas"], 401);
        }

        $usuario = $request->user();
        // generar token
        $token = $usuario->createToken('auth_token')->plainTextToken;
        return response()->json(["access_token" => $token, "usuario" => $usuario], 200);

    }

    public function register(Request $request){

        $request->validate([
           'name' => 'required|string',
           'email' => 'required|email',
           'password' => 'required', 
        ]);

        // guardar
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->save();

        return response()->json(["message" => "Usuario creado con exito"], 201);
    }


    public function profile(Request $request){
        $usuario = $request->user();

        return response()->json($usuario, 200);
    }


    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json(["message" => "Sesion Cerrada"], 200);

    }
}
