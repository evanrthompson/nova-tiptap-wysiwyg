<?php

use Evanrthompson\NovaTiptapWysiwyg\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::post('/upload', UploadController::class)->name('nova-tiptap-wysiwyg.upload');

Route::get('/css/wysiwyg.css', function () {
    $path = __DIR__.'/../dist/css/wysiwyg.css';

    return response()->file($path, [
        'Content-Type' => 'text/css',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('nova-tiptap-wysiwyg.css');
