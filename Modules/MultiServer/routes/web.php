<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiServer\Http\Controllers\MultiServerController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('multiservers', MultiServerController::class)->names('multiserver');
});
