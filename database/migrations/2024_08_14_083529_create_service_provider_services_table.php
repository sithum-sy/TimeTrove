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
        Schema::create('service_provider_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_category_id')->constrained('service_categories')->onDelete('cascade');
            $table->text('description');
            $table->string('availability');
            $table->decimal('rate', 8, 2);
            $table->string('city');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_services');
    }
};
