<?php

namespace App\Http\Controllers;

use App\Models\Helper;
use App\Models\Task;
use Illuminate\Http\Request;

class HelperDashboardController extends Controller
{
    public function index($id)
    {
        // 1. Get Helper Profile & Stats
        $helper = Helper::find($id);

        // 2. Get Available Tasks (Only 'open' ones)
        $tasks = Task::where('status', 'open')->get();

        // 3. Send everything to React
        return response()->json([
            'profile' => $helper,
            'availableTasks' => $tasks
        ]);
    }
}