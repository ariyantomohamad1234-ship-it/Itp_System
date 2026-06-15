<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Mengambil project yang berelasi dengan user
        $projects = $user->projects()->get();
        
        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }
}
