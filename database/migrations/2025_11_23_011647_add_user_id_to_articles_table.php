<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migrationクラスを拡張した新しいクラスを作成
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // パラメータなしのメソッドupを定義
    public function up(): void
    {
        // Schemaクラスのtableメソッドを実行。articlesというテーブルを作成。中身をどのようにするかはパラメータで指定。
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    // パラメータなしのメソッドdownを定義
    public function down(): void
    {
        // Schemaクラスのtableメソッドを実行しarticlesテーブルを作成。テーブル内容は、user_idカラムを削除
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
