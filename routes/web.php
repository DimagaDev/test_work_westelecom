<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [BalanceController::class, 'index'])->name('calculator');
