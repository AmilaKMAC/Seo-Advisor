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
Schema::create('domain_recommendations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seo_report_id')->constrained()->cascadeOnDelete();

    $table->string('domain');
    $table->boolean('available')->default(false);
    $table->integer('score')->nullable();
    $table->text('reason')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_recomendations');
    }
};
