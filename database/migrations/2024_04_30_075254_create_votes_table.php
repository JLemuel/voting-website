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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id'); // Foreign key column
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->unsignedBigInteger('voted_by');
            // Add other vote-related fields as needed
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('participant_id')->references('id')->on('room_participants')->onDelete('cascade');
            $table->foreign('voted_by')->references('id')->on('room_participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};