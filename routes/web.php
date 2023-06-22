<?php

use App\Bots\pozor_baraholka_bot\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'telegram:pozorbottestbot'])->name('pozor_baraholka_bot.')->group(function () {
    Route::resource('/pozor_baraholka_bot/announcement', AnnouncementController::class);
});
