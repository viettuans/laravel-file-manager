<?php

use Illuminate\Support\Facades\Route;
use Viettuans\FileManager\Controllers\MediaController;

$config = config('filemanager.routes');

Route::prefix($config['prefix'] ?? 'api/filemanager')
    ->middleware($config['middleware'] ?? ['api'])
    ->name($config['name_prefix'] ?? 'filemanager.')
    ->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/', [MediaController::class, 'store'])->name('store');
        Route::get('/{id}', [MediaController::class, 'show'])->name('show');
        Route::delete('/', [MediaController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/thumbnail', [MediaController::class, 'generateThumbnail'])->name('thumbnail');
    });