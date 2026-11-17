<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registration_id')->constrained('registrations')->restrictOnDelete();
            $table->foreignUuid('ticket_id')->constrained('tickets')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price_rupiah');
            $table->unsignedInteger('subtotal_rupiah');
            $table->timestamps();

            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')->restrictOnDelete();

            $table->unique(['registration_id', 'ticket_id']);
            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_tickets');
    }
};
