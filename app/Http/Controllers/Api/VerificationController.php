<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:app_users,email',
            'code'  => 'required|string|size:5',
        ]);

        $user = AppUser::where('email', $data['email'])->first();

        if ($user->is_verified) {
            return response()->json(['message' => 'Este usuario ya está verificado.'], 200);
        }

        if ($user->verification_code !== $data['code']) {
            return response()->json(['message' => 'Código de verificación incorrecto.'], 422);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Verificación exitosa.'], 200);
    }

    // (Opcional) Reenviar código de verificación
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:app_users,email']);

        $user = AppUser::where('email', $request->email)->first();

        if ($user->is_verified) {
            return response()->json(['message' => 'Este usuario ya está verificado.'], 200);
        }

        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $user->verification_code = $code;
        $user->save();

        // Reenviar correo
        Mail::to($user->email)->send(new \App\Mail\UserVerificationCodeMail($code));

        return response()->json(['message' => 'Código reenviado al correo electrónico.']);
    }
}
