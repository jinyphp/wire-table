<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


// WireTable 업로드한 이미지 보기
// public
/*
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
*/



/**
 * Table Assets
 * 테이블에서 업로드한 aseet 파일을 반환하는 response
 */
Route::middleware(['web'])
->name('upload.')
->prefix('/upload')->group(function () {
    Route::get('{any}', [
        \Jiny\WireTable\Http\Controllers\AssetsController::class,
        'index'])->where('any', '.*');
});
