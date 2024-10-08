<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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


    public function resetPassword(Request $request){

        $request->validate([
            'email' => 'required|email',
        ]);


        /*$token = Str::random(64);

        $dest = $request->email;

        Mail::send('resetP', ['token', $token], function ($message) use ($dest) {
            $message->to($dest);
            $message->subject('Esto esta rico');
        });

        return response()->json(["message" => "Enviamos un correo"]);*/

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return ["status" => __($status)];
        }

        
    }
}
