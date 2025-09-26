<?php
// database/migrations/create_tickets_table.php

use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g., TICK-001
            $table->string('subject');
            $table->string('status')->default(TicketStatus::NEW);

            // For authenticated users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // For guest users (from contact form)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
