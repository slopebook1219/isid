<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();

            // 紐づくゲーム・問題・チーム
            $table->foreignId('game_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->foreignId('question_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('team_id')
                  ->constrained()
                  ->onDelete('cascade');

            // 回答値（中央値算出に使う）
            $table->decimal('answer_value', 10, 2);

            $table->timestamps();

            // 1つのチームが同じ問題に複数回答しないよう制約を追加
            $table->unique(['game_id', 'question_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
