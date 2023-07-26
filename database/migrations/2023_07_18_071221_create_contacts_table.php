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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number');
            $table->string('country_code');
            $table->foreignId("user_id");

            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->date('birthday')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->default(config('info.default_contact_photo'));
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
