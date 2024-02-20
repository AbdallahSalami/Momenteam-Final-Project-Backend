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
    {// ... other code ...

Schema::create('certificates', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('userId')->useCurrent();
    $table->unsignedBigInteger('eventId')->nullable(); // Add this line
    $table->string('title');
    $table->string('description');
    $table->timestamp('date');
    $table->unsignedBigInteger('secretaryId')->useCurrent()->nullable();
    $table->timestamp('secretaryFirstDate')->nullable();
    $table->unsignedBigInteger('managerId')->useCurrent()->nullable();
    $table->timestamp('managerApprovelDate')->nullable();
    $table->timestamp('secretarySecondDate')->nullable();
    $table->string('qrCode')->nullable();
    $table->enum('status', ['sended', 'approved', 'waiting','pending']);
    $table->timestamps();
});

// ... other code ...

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
