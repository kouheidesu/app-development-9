<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ゲスト用ルート（ログインしていない人のみアクセス可能）
Route::middleware('guest')->group(function () {
    // ログイン画面表示
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    // ログイン処理
    Route::post('/login', [AuthController::class, 'login']);

    // 登録画面表示
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    // 登録処理
    Route::post('/register', [AuthController::class, 'register']);
});

// 認証必須ルート（ログインしている人のみアクセス可能）
Route::middleware('auth')->group(function () {
    // /を叩くとArticleControllerクラスのindexメソッドを実行
    Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
    // 記事一覧
    Route::get('/articles', [ArticleController::class, 'index']);
    // 記事作成
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    // 記事更新
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    // 記事削除
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    // ログアウト
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
