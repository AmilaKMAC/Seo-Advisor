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
    Schema::create('seo_reports', function (Blueprint $table) {
    $table->id();
    $table->string('project_name')->nullable();
    $table->string('website_url');

    $table->integer('on_page_score')->nullable();
    $table->integer('technical_score')->nullable();
    $table->integer('off_page_score')->nullable();
    $table->integer('overall_score')->nullable();

    $table->json('raw_seo_data')->nullable();
    $table->json('page_speed_data')->nullable();
    $table->json('ai_fixes')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_reports');
    }
};
