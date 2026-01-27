<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiServer\Http\Controllers\MultiServerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('multiservers', MultiServerController::class)->names('multiserver');
});
