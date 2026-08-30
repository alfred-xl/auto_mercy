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
        Schema::create('cars', function (Blueprint $table): void {
            $table->id();
            $table->string('stock_number')->nullable()->unique();
            $table->string('slug')->nullable()->unique();
            $table->foreignId('make_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('car_model_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('body_type_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('car_stand_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('trim')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedBigInteger('price_amount')->nullable();
            $table->string('currency', 3)->default('NGN');
            $table->unsignedInteger('mileage')->nullable();
            $table->string('mileage_unit', 8)->nullable();
            $table->string('condition')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('drivetrain')->nullable();
            $table->string('engine')->nullable();
            $table->string('exterior_colour')->nullable();
            $table->string('interior_colour')->nullable();
            $table->text('description')->nullable();
            $table->json('supplemental_specs')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('video_url', 2048)->nullable();
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 170)->nullable();
            $table->string('canonical_override', 2048)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('reservation_expires_at')->nullable();
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at', 'id'], 'cars_status_published_id_idx');
            $table->index(['status', 'make_id', 'price_amount'], 'cars_status_make_price_idx');
            $table->index(['status', 'car_model_id'], 'cars_status_model_idx');
            $table->index(['status', 'body_type_id', 'price_amount'], 'cars_status_body_price_idx');
            $table->index(['status', 'car_stand_id'], 'cars_status_stand_idx');
            $table->index(['status', 'year'], 'cars_status_year_idx');
            $table->index(['status', 'mileage'], 'cars_status_mileage_idx');
            $table->index(['is_featured', 'status', 'published_at'], 'cars_featured_status_published_idx');
            $table->index(['status', 'reservation_expires_at'], 'cars_status_reservation_expiry_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
