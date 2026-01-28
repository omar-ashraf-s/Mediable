<?php

use Illuminate\Support\Facades\Route;
use Mabrouk\Mediable\Http\Controllers\MediaController;

Route::put('media/{medium}', [MediaController::class, 'update'])->name('media.update');
Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
