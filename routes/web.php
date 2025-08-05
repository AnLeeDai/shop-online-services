<?php

use App\Http\Controllers\ApiHealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ApiHealthCheckController::class, 'index']);
