<?php

use App\Bots\pozor_baraholka_bot\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'telegram:pozor_baraholka_bot'])->prefix('/pozor_baraholka_bot')->name('pozor_baraholka_bot.')->group(function () {
    Route::resource('/announcement', AnnouncementController::class);
});
