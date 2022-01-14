<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $input = $request->all();

        $validation = [
            'nama' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ];

        $validator = Validator::make($input, $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = new User;
        $user->nama = $request->input('nama');
        $user->email = $request->input('email');
        $passwordP = $request->input('password');
        $user->password = app('hash')->make($passwordP);
        $user->save();

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $validation = [
            'email' => 'required|string',
            'password' => 'required|string',
        ];

        $validator = Validator::make($input, $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }

    public function forgot(Request $request)
    {
        $email = $request->input('email');

        $user = User::where(['email' => $email])->first();
        if ($user) {
            return response()->json([
                'email' => $email,
                'link_reset_password' => env('APP_URL').'/auth/password/new/'.$user['email']
            ]);
        }

        return response()->json([
            'email' => 'Email Tidak Ditemukan'
        ]);

    }

    public function newPass(Request $request, $id)
    {
        $user = User::find($id) ;

        $passwordP = $request->input('password');
        $user->password = app('hash')->make($passwordP);
        $user->save();

        return response()->json([
            'message' => 'Password has been change',
            'link_to_login' => env('APP_URL').'/auth/login/'
        ]); 
    }
}
