<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubjectTable extends Migration
{
    public function up()
    {
        Schema::create('user_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users') // Explicitly specify the users table if necessary
                  ->onDelete('cascade');
            $table->foreignId('subject_id')
                  ->constrained('subjects') // Explicitly specify the subjects table if necessary
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_subject');
    }
}
