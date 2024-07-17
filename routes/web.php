<?php

use App\Livewire\UploadImage;
use Illuminate\Support\Facades\Route;

Route::get('/', UploadImage::class);
Route::post('/', UploadImage::class);
Route::get('/info', [UploadImage::class, 'createStoryFromImg']);
