<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\AppUser;
use App\Mail\PasswordResetCodeMail;

class PasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:app_users,email',
        ], [], [
            'email' => 'correo electrónico',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validación fallida.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = AppUser::where('email', $request->email)->first();

        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);

        $user->update([
            'password_reset_code' => $code,
            'password_reset_code_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new PasswordResetCodeMail($code));

        return response()->json([
            'status'  => true,
            'message' => 'Código enviado por correo electrónico.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:app_users,email',
            'code'                  => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
        ], [], [
            'email'    => 'correo electrónico',
            'code'     => 'código',
            'password' => 'contraseña',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validación fallida.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = AppUser::where('email', $request->email)->first();

        if (
            ! $user ||
            $user->password_reset_code !== $request->code ||
            now()->gt($user->password_reset_code_expires_at)
        ) {
            return response()->json([
                'status'  => false,
                'message' => 'Código inválido o expirado.',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_code' => null,
            'password_reset_code_expires_at' => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }
}
