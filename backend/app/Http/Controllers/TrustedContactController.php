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
        $validated = $request->validate([
            'contact_name' => 'required|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $contact = TrustedContact::create([
            'user_id' => Auth::id(),
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'status' => 'pending', // Would send invite logic here
        ]);

        return response()->json($contact, 201);
    }
}
