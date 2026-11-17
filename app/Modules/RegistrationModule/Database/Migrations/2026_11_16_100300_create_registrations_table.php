<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('idempotency_key')->nullable();
            $table->unsignedInteger('total_rupiah');
            $table->string('status')->default('completed');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')->restrictOnDelete();

            $table->unique(['user_id', 'idempotency_key']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
