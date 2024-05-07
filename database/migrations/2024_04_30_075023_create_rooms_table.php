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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 6)->unique();
            $table->string('room_name');
            $table->boolean('set_timer')->default(false); // Whether to set a timer for creating questions
            $table->unsignedInteger('timer_seconds')->nullable(); // Number of seconds for the timer (nullable if set_timer is false)
            $table->boolean('pick_random_participant')->default(true); // Option to enable/disable picking a random participant to create the question
            $table->unsignedInteger('num_questions');
            $table->unsignedTinyInteger('question_level');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
