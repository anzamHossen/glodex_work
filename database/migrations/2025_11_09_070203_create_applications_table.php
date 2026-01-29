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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment("Admin Panel user's id to whom this application will pop up/ display");
            $table->foreignId('course_id');
            $table->foreignId('student_id');
            $table->string('sent_by')->nullable();
            $table->integer('application_code');
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable()->comment("the id of the person who is creating this application/ auth id");
            $table->text('intake_year')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
