<?php

use Illuminate\Support\Facades\Route;
use Btab\Controllers\Admin\SyncController;

Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::post('/btab/sync', [SyncController::class, 'sync'])->name('btab.sync');
});
