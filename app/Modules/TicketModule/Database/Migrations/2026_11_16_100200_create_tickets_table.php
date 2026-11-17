<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained('events')->restrictOnDelete();
            $table->string('name');
            $table->unsignedInteger('price_rupiah');
            $table->unsignedInteger('initial_quota');
            $table->unsignedInteger('remaining_quota');
            $table->dateTime('sales_start');
            $table->dateTime('sales_end');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')->restrictOnDelete();

            $table->index(['event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
