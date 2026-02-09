<?php

use App\Http\Controllers\Api\HouseController;
use Illuminate\Support\Facades\Route;

Route::get('/houses', [HouseController::class, 'index']);
