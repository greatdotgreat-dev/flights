<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_provided', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');         // e.g. "Flight + Hotel"
            $table->string('slug')->unique();        // e.g. "flight-hotel"
            $table->text('description')->nullable(); // optional description
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_provided');
    }
};
