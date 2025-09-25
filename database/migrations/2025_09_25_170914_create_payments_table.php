<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default(config('app.currency_code'));
            $table->string('payment_method')->default(PaymentMethod::CREDIT_CARD->value);
            $table->string('status')->default(PaymentStatus::PENDING->value);
            $table->string('transaction_id')->nullable();
            $table->string('gateway_response')->nullable();
            $table->json('gateway_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reservation_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('payment_number');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
