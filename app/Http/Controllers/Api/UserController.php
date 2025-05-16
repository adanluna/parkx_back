<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserDeletedMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|unique:app_users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $user]);
    }

    public function deleteUser(Request $request)
    {
        $user = $request->user();

        if ($user->deleted_at) {
            return response()->json([
                'status'  => false,
                'message' => 'Este usuario ya fue eliminado previamente.',
            ], 400);
        }

        $originalEmail = $user->email;

        // Marcar como eliminado
        $user->email = $user->email . '.deleted';
        $user->deleted_at = now();
        $user->save();

        // Eliminar todos los tokens (logout forzado)
        $user->tokens()->delete();

        // Enviar correo de confirmación al email original
        Mail::to($originalEmail)->send(new UserDeletedMail());

        return response()->json([
            'status'  => true,
            'message' => 'Usuario desactivado correctamente. Se ha enviado una confirmación por correo.',
        ]);
    }
}
