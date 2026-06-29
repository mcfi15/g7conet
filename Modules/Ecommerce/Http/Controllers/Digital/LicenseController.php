<?php

namespace Modules\Ecommerce\Http\Controllers\Digital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Ecommerce\Entities\Digital\License;

class LicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:30,1')->only(['validateLicense', 'activate', 'deactivate']);
    }

    public function validateLicense(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|max:64',
            'domain' => 'required|string|max:255',
        ]);

        $license = License::with('product')->where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json(['valid' => false, 'message' => 'License key not found.'], 404);
        }

        if ($license->is_locked) {
            return response()->json(['valid' => false, 'message' => 'License has been revoked.'], 403);
        }

        if ($license->isExpired()) {
            return response()->json(['valid' => false, 'message' => 'License has expired.'], 403);
        }

        $domains = $license->activated_domains ?? [];
        $isActive = in_array($request->domain, $domains);

        return response()->json([
            'valid' => true,
            'activated' => $isActive,
            'product' => $license->product->name ?? 'Unknown',
            'license_type' => $license->license_type,
            'activations_used' => $license->activations_count,
            'activation_limit' => $license->activation_limit,
        ]);
    }

    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|max:64',
            'domain' => 'required|string|max:255',
        ]);

        $rateKey = 'activate:' . $request->license_key;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return response()->json([
                'success' => false,
                'message' => "Too many activation attempts. Try again in {$seconds} seconds."
            ], 429);
        }
        RateLimiter::hit($rateKey, 300);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json(['success' => false, 'message' => 'License key not found.'], 404);
        }

        if (!$license->canActivate()) {
            $reason = $license->is_locked ? 'License has been revoked.'
                : ($license->isExpired() ? 'License has expired.'
                : 'Activation limit reached (' . $license->activation_limit . ').');
            return response()->json(['success' => false, 'message' => $reason], 403);
        }

        $domains = $license->activated_domains ?? [];

        if (in_array($request->domain, $domains)) {
            return response()->json([
                'success' => true,
                'message' => 'Domain already activated.',
                'already_activated' => true,
            ]);
        }

        $domains[] = $request->domain;
        $license->activated_domains = $domains;
        $license->activations_count = count($domains);
        $license->save();

        return response()->json([
            'success' => true,
            'message' => 'Domain activated successfully.',
            'activations_used' => $license->activations_count,
            'activation_limit' => $license->activation_limit,
        ]);
    }

    public function deactivate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string|max:64',
            'domain' => 'required|string|max:255',
        ]);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json(['success' => false, 'message' => 'License key not found.'], 404);
        }

        $domains = $license->activated_domains ?? [];
        $domains = array_filter($domains, fn($d) => $d !== $request->domain);
        $license->activated_domains = array_values($domains);
        $license->activations_count = count($domains);
        $license->save();

        return response()->json([
            'success' => true,
            'message' => 'Domain deactivated successfully.',
            'activations_used' => $license->activations_count,
        ]);
    }

    public function show(License $license)
    {
        if ($license->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json([
            'license_key' => $license->license_key,
            'license_type' => $license->license_type,
            'product_name' => $license->product->name ?? 'Unknown',
            'activated_domains' => $license->activated_domains ?? [],
            'activations_count' => $license->activations_count,
            'activation_limit' => $license->activation_limit,
            'expires_at' => $license->expires_at?->toDateString(),
            'is_locked' => $license->is_locked,
        ]);
    }
}
