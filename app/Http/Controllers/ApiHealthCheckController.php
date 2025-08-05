<?php

namespace App\Http\Controllers;

class ApiHealthCheckController extends Controller
{
    public function index()
    {
        return redirect("/up");
    }
}