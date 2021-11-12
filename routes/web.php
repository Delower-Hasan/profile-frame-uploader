<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileImageController;


Route::get('/', [ProfileImageController::class, 'index']);
Route::post('/uploads-profile', [ProfileImageController::class, 'uploads'])->name('uploads.profile');
