<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('draft');
            $table->string('banner_path')->nullable();

            $table->timestamps();
            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')->restrictOnDelete();

            $table->index(['status', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
