<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Return payments where user is payer or payee
        return Payment::where('payer_id', $request->user()->id)
            ->orWhere('payee_id', $request->user()->id)
            ->with(['task:id,title', 'payer:id,name', 'payee:id,name'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'payee_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            ...$validated,
            'payer_id' => $request->user()->id,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return response()->json($payment, 201);
    }
}
