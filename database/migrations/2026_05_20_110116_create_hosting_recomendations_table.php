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
Schema::create('hosting_recommendations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seo_report_id')->constrained()->cascadeOnDelete();

    $table->string('platform');
    $table->text('reason')->nullable();
    $table->integer('score')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_recomendations');
    }
};
