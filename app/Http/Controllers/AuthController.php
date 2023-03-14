<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        //autenticação email e senha
        $credenciais = $request->all('email', 'password');
        $token = auth('api')->attempt($credenciais);
        
        if($token){
            return response()->json(['token' => $token]);
        }else{
            return response()->json(['erro' => 'Usuário ou senha inválidos!'], 403);
        }

        //retornar um json Web token
        return $token;
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => 'O logout foi realizado com sucesso']);
    }

    public function refresh(){
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);
    }

    public function me(){
        return response()->json(auth()->user());
    }
}
