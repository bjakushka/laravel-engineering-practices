<?php

use App\Http\Controllers\ReadingListController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReadingListController::class, 'index'])
    ->name('reading_list.index');
