<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_tag', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained('events')->restrictOnDelete();
            $table->foreignUuid('tag_id')->constrained('tags')->restrictOnDelete();
            $table->unique(['event_id', 'tag_id']);

            $table->timestamps();
            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_tag');
    }
};
