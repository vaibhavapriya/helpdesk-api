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
        Schema::create('mailconfigs', function (Blueprint $table) {
            $table->id();
            $table->string('mail_from_name')->nullable(); // optional sender name
            $table->string('mail_from_address'); // required sender email

            $table->string('host');        // SMTP host
            $table->unsignedSmallInteger('port'); // SMTP port
            $table->string('encryption')->nullable(); // tls, ssl, etc.

            $table->string('username')->nullable();
            $table->string('password')->nullable(); // Should encrypt on model level

            $table->boolean('active')->default(false); // to toggle between configs

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
