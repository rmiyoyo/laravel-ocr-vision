<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OCRController;

Route::get('/', [OCRController::class, 'index'])->name('ocr.index');
Route::post('/upload', [OCRController::class, 'upload'])->name('ocr.upload');
Route::get('/result/{id}', [OCRController::class, 'showResult'])->name('ocr.result');
Route::post('/save-text/{id}', [OCRController::class, 'saveText'])->name('ocr.save');