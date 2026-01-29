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
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id');
            $table->string('university_name', 255);
            $table->string('university_city', 150);
            $table->string('admission_email', 255);
            $table->string('admission_phone', 20);  
            $table->string('website_link');
            $table->text('address');
            $table->string('commission_for_us');
            $table->string('commission_for_agent');
            $table->longText('logo');
            $table->longText('cover_image');
            $table->longText('description');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
