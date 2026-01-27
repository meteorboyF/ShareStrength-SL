<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccessibilitySettingsController extends Controller
{
    private function currentActor(): ?array
    {
        if (Auth::guard('admin')->check()) {
            return ['actor_type' => 'admin', 'actor_id' => Auth::guard('admin')->id()];
        }
        if (Auth::guard('provider')->check()) {
            return ['actor_type' => 'provider', 'actor_id' => Auth::guard('provider')->id()];
        }
        if (Auth::guard('web')->check()) {
            return ['actor_type' => 'user', 'actor_id' => Auth::guard('web')->id()];
        }

        return null;
    }

    public function show()
    {
        $actor = $this->currentActor();
        if (!$actor) {
            return response()->json(['settings' => null], 200);
        }

        $row = DB::table('accessibility_settings')
            ->where('actor_type', $actor['actor_type'])
            ->where('actor_id', $actor['actor_id'])
            ->first();

        return response()->json([
            'settings' => $row ? json_decode((string) $row->settings, true) : null,
        ]);
    }

    public function update(Request $request)
    {
        $actor = $this->currentActor();
        if (!$actor) {
            return response()->json(['message' => 'Not authenticated.'], 401);
        }

        $validated = $request->validate([
            'fontSize' => 'required|string|in:small,medium,large,x-large',
            'displayMode' => 'required|string|in:normal,dark-mode,high-contrast,comfort-mode',
            'eyeTrackingEnabled' => 'required|boolean',
        ]);

        $settings = [
            'fontSize' => $validated['fontSize'],
            'displayMode' => $validated['displayMode'],
            'eyeTrackingEnabled' => (bool) $validated['eyeTrackingEnabled'],
        ];

        DB::table('accessibility_settings')->updateOrInsert(
            [
                'actor_type' => $actor['actor_type'],
                'actor_id' => $actor['actor_id'],
            ],
            [
                'settings' => json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        return response()->json(['ok' => true]);
    }
}
