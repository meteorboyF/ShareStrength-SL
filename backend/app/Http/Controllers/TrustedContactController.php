<?php

namespace App\Http\Controllers;

use App\Models\TrustedContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrustedContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can view trusted contacts'], 403);
        }

        return response()->json($user->trustedContacts);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can add trusted contacts'], 403);
        }

        // Accept both 'name' (frontend) and 'contact_name' patterns
        $validated = $request->validate([
            'name' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'relation' => 'nullable|string',
            'phone' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'email' => 'nullable|email',
            'contact_email' => 'nullable|email',
        ]);

        $contactName = $validated['name'] ?? $validated['contact_name'];
        if (!$contactName) {
            return response()->json(['message' => 'Name is required'], 422);
        }

        $contact = TrustedContact::create([
            'user_id' => $user->getKey(),
            'contact_name' => $contactName,
            'relation' => $validated['relation'] ?? null,
            'contact_email' => $validated['email'] ?? $validated['contact_email'] ?? null,
            'contact_phone' => $validated['phone'] ?? $validated['contact_phone'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($contact, 201);
    }
}
