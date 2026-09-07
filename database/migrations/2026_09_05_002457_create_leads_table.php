<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('phone', 32);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 32)->default('website');
            $table->string('status', 32)->default('new');
            $table->timestamp('follow_up_at')->nullable();
            $table->timestamp('inspection_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('car_id');
            $table->index(['status', 'created_at']);
            $table->index(['follow_up_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
