<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use App\Notifications\CodeValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Stripe\StripeClient;

class GetVersionController extends Controller
{
    public $app_version;
    public $app_apple_revision;

    public function __construct()
    {
        $this->app_version = env('APP_VERSION');
        $this->app_apple_revision = env('APP_APPLE_REVISION');
    }

    public function version(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => ['app_version' => $this->app_version, 'app_apple_revision' => $this->app_apple_revision]
        ]);
    }
}
