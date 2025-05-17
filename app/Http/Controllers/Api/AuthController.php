<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\AppUser;
use App\Mail\UserVerificationCodeMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:app_users,email',
            'password' => 'required|string|min:6',
            'apellidos' => 'nullable|string|max:255',
        ], [], [
            'name'     => 'nombre',
            'email'    => 'correo electrónico',
            'password' => 'contraseña',
        ]);

        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        $user = AppUser::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        Mail::to($user->email)->send(new UserVerificationCodeMail($code));

        return response()->json([
            'status'  => true,
            'message' => 'Usuario registrado. Revisa tu correo para el código de verificación.',
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [], [
            'email'    => 'correo electrónico',
            'password' => 'contraseña',
        ]);

        $user = AppUser::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        if (! $user->is_verified) {
            return response()->json([
                'status'  => false,
                'message' => 'Tu cuenta no ha sido verificada.',
            ], 403);
        }

        $token = $user->createToken('app_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Inicio de sesión exitoso.',
            'data' => [
                'user'  => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
