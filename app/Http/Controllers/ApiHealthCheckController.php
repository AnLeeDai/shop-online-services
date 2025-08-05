<?php

namespace App\Http\Controllers;

class ApiHealthCheckController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'SERVER IS ALIVE', 'data' => []]);
    }
}