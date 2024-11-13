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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_card_id')->constrained('class_cards')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->tinyInteger('type'); // 1 for performance task, 2 for quizzes, 3 for recitation
            $table->tinyInteger('item'); // 1 item number per classcard
            $table->float('score', 8, 2); // Float with precision (8 digits total, 2 after decimal point)
            $table->tinyInteger('term'); // 1 for prelim, 2 for midterm, 3 for finals
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
