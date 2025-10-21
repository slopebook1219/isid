<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // user_id カラムを追加（外部キー制約付き）
            $table->foreignId('user_id')
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // 外部キーを先に削除してからカラム削除
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
