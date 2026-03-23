<?php

use Illuminate\Support\Facades\Route;
use Modules\Owner\Http\Controllers\OwnerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('owners', OwnerController::class)->names('owner');
});
