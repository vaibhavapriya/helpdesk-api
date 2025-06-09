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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('error_message');
            $table->text('stack_trace')->nullable();
            $table->string('user_id')->nullable(); // Optional: to store the user who encountered the error
            $table->string('method')->nullable(); // Optional: API method name
            $table->string('route')->nullable(); // Optional: route the user was accessing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
