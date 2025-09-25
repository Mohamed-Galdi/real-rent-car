<?php

use App\Enums\CarColor;
use App\Enums\CarStatus;
use App\Enums\FuelType;
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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('license_plate')->unique();
            $table->string('color')->default(CarColor::WHITE->value);
            $table->decimal('price_per_day', 10, 2);
            $table->integer('mileage');
            $table->enum('transmission', ['automatic', 'manual']);
            $table->integer('seats');
            $table->string('fuel_type')->default(FuelType::GASOLINE->value);
            $table->text('description')->nullable();
            $table->string('status')->default(CarStatus::AVAILABLE->value);
            $table->timestamps();
            $table->softDeletes();
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
