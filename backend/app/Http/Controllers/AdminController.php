<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Helper;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Get all users with search and filter
     */
    public function getUserList(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->has('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        // Filter by suspended status
        if ($request->has('is_suspended')) {
            $query->where('is_suspended', $request->is_suspended === 'true');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($users);
    }

    /**
     * Verify a user
     */
    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_status = 'verified';
        $user->save();

        return response()->json([
            'message' => 'User verified successfully',
            'user' => $user
        ]);
    }

    /**
     * Suspend/Unsuspend a user
     */
    public function suspendUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_suspended = $request->input('suspend', true);
        $user->save();

        return response()->json([
            'message' => $user->is_suspended ? 'User suspended' : 'User unsuspended',
            'user' => $user
        ]);
    }

    /**
     * Get all helpers with search and filter
     */
    public function getHelperList(Request $request)
    {
        $query = Helper::query();

        // Search byname or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified === 'true');
        }

        // Filter by suspended status
        if ($request->has('is_suspended')) {
            $query->where('is_suspended', $request->is_suspended === 'true');
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        $helpers = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($helpers);
    }

    /**
     * Verify a helper
     */
    public function verifyHelper($id)
    {
        $helper = Helper::findOrFail($id);
        $helper->is_verified = true;
        $helper->save();

        return response()->json([
            'message' => 'Helper verified successfully',
            'helper' => $helper
        ]);
    }

    /**
     * Suspend/Unsuspend a helper
     */
    public function suspendHelper(Request $request, $id)
    {
        $helper = Helper::findOrFail($id);
        $helper->is_suspended = $request->input('suspend', true);
        $helper->save();

        return response()->json([
            'message' => $helper->is_suspended ? 'Helper suspended' : 'Helper unsuspended',
            'helper' => $helper
        ]);
    }

    /**
     * Get all payments with search and filter
     */
    public function getPaymentList(Request $request)
    {
        $query = Payment::with(['task:id,title', 'payer:id,name', 'payee:id,name']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        // Filter by minimum amount
        if ($request->has('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($payments);
    }

    /**
     * Approve a pending payment (mark as paid)
     */
    public function approvePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 'paid';
        $payment->paid_at = now();
        $payment->save();

        return response()->json([
            'message' => 'Payment approved successfully',
            'payment' => $payment
        ]);
    }

    /**
     * Get all reviews with search and filter
     */
    public function getReviewList(Request $request)
    {
        $query = Review::with(['task:id,title', 'reviewer:id,name', 'reviewee:id,name']);

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by minimum rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Search in comments
        if ($request->has('search')) {
            $query->where('comment', 'like', "%{$request->search}%");
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($reviews);
    }

    /**
     * Delete a review
     */
    public function deleteReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_suspended', false)->count(),
            'total_helpers' => Helper::count(),
            'verified_helpers' => Helper::where('is_verified', true)->count(),
            'total_tasks' => \App\Models\Task::count(),
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'total_reviews' => Review::count(),
            'average_rating' => Review::avg('rating'),
        ];

        return response()->json($stats);
    }
}
