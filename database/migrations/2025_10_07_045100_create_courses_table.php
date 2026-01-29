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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id');
            $table->foreignId('country_id');
            $table->foreignId('course_program_id');
            $table->json('intake_month_id');
            $table->string('course_name');
            $table->string('application_fee');
            $table->string('tuition_fee_per_year');
            $table->string('program_length');
            $table->longText('course_photo');
            $table->longText('course_details');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
