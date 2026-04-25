<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SSOController extends Controller
{
    /**
     * Authenticate a user coming from the mobile app.
     */
    public function remoteLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'sig' => 'required|string',
        ]);

        $secret = config('services.sso.secret');
        // Normalize email for consistent signature verification
        $email = strtolower(trim($request->email));
        $expectedSig = hash('sha256', $email . $secret);

        if ($request->sig !== $expectedSig) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
                'debug' => [
                    'received' => $request->sig,
                    'expected' => $expectedSig,
                    'email_used' => $email
                ]
            ], 403);
        }

        // Find or create the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'phone' => $request->phone ?? '',
                'password' => Hash::make(Str::random(16)),
                'is_admin' => false,
            ]);
        }

        // Mark as app session
        session(['is_from_app' => true]);

        // Login the user
        Auth::login($user, true);

        // Redirect to requested path or default to /cars
        $redirectPath = $request->query('redirect', '/cars');
        
        // Security: Only allow internal redirects
        if (str_starts_with($redirectPath, 'http')) {
             $appUrl = config('app.url');
             $appHost = parse_url($appUrl, PHP_URL_HOST);
             $redirectHost = parse_url($redirectPath, PHP_URL_HOST);
             
             if ($redirectHost !== $appHost) {
                 $redirectPath = '/cars';
             }
        } elseif (!str_starts_with($redirectPath, '/')) {
            $redirectPath = '/cars';
        }

        return redirect()->to($redirectPath);
    }
}
