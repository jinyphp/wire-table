<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


// WireTable 업로드한 이미지 보기
// public
Route::middleware(['web'])
->name('image.')
->prefix('/images')->group(function () {
    Route::get('/public/{path}/{filename}',[
        \Jiny\WireTable\Http\Controllers\ImageView::class,"index"
    ])->name("images.public");
});
// private (인증필요)
Route::middleware(['web','auth:sanctum', 'verified'])
->name('image.')
->prefix('/images')->group(function () {
    Route::get('/private/{path}/{filename}',[
        \Jiny\WireTable\Http\Controllers\ImageView::class,"index"
    ]);
});
