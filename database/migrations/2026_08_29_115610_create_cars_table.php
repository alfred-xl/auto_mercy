<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table): void {
            $table->id();
            $table->string('stock_number', 20)->unique();
            $table->string('slug')->unique();
            $table->string('listing_category', 32);
            $table->string('make', 100);
            $table->string('model', 100);
            $table->string('trim')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('body_type', 100)->nullable();
            $table->unsignedBigInteger('price_amount');
            $table->unsignedBigInteger('previous_price_amount')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->string('mileage_unit', 8)->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('drivetrain')->nullable();
            $table->string('engine')->nullable();
            $table->string('exterior_colour')->nullable();
            $table->string('interior_colour')->nullable();
            $table->json('features')->nullable();
            $table->text('description');
            $table->string('status', 32)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['status', 'make', 'model']);
            $table->index(['status', 'price_amount']);
            $table->index(['listing_category', 'status']);
            $table->index(['is_featured', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
