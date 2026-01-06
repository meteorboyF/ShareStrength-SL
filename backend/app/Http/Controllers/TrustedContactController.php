<?php

namespace App\Http\Controllers;

use App\Models\TrustedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrustedContactController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->trustedContacts);
    }

    public function store(Request $request)
    {
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
            'user_id' => Auth::id(),
            'contact_name' => $contactName,
            'relation' => $validated['relation'] ?? null,
            'contact_email' => $validated['email'] ?? $validated['contact_email'] ?? null,
            'contact_phone' => $validated['phone'] ?? $validated['contact_phone'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($contact, 201);
    }
}
