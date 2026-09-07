<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('path')->unique();
            $table->json('derivatives')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('processing_status', 32)->default('pending')->index();
            $table->text('processing_error')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_images');
    }
};
