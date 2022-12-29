<?php

use Illuminate\Support\Facades\Route;
use Viettuans\FileManager\Controllers\MediaController;

Route::prefix('media')->as('media.')->group(function (){
    Route::get('list', [MediaController::class, 'getList'])->name('list');
    Route::post('upload', [MediaController::class, 'upload'])->name('upload');
    Route::post('delete', [MediaController::class, 'delete'])->name('delete');
});