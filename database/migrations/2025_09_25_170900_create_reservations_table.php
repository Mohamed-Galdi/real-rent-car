<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReservationStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('pickup_time')->default('09:00');
            $table->time('return_time')->default('18:00');
            $table->string('pickup_location')->nullable();
            $table->string('return_location')->nullable();
            $table->integer('total_days');
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default(ReservationStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['car_id', 'start_date', 'end_date']);
            $table->index('reservation_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
