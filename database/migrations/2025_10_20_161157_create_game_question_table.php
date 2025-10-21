<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // 同じ組み合わせを重複して登録しないようにする
            $table->unique(['game_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_question');
    }
};
