<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

// /を叩くとArticleControllerクラスのindexメソッドを実行。パラメータでname関数を実行。
Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
// /articlesを叩くとArticleControllerクラスのstoreメソッドを実行。パラメータでname関数を実行。
Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
// /articlesを叩くとArticleControllerクラスのupdateメソッドを実行。パラメータでname関数を実行。(updateをarticles.updateという名前にしている)
Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
// /articlesを叩くとArticleControllerクラスのdestroyメソッドを実行。パラメータでname関数を実行。(updateをarticles.updateという名前にしている)
Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
