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

        $secret = env('SSO_SECRET');
        $expectedSig = hash('sha256', $request->email . $secret);
        dd($request->sig . ' ' . $expectedSig);
        if ($request->sig !== $expectedSig) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
            ], 403);
        }

        // Find or create the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? '',
                'password' => Hash::make(Str::random(16)),
                'is_admin' => false,
            ]);
        }

        // Mark as app session
        session(['is_from_app' => true]);

        // Login the user
        Auth::login($user, true);

        // Redirect to the booking entry point
        return redirect()->to('/cars');
    }
}
