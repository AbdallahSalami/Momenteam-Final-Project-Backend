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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memberId')->useCurrent();
            $table->string('title');
            $table->string('description');
            $table->unsignedBigInteger('scientificAuditorId')->useCurrent()->nullable();
            $table->timestamp('scientificAuditorApprovelDate')->nullable();
            $table->unsignedBigInteger('linguisticCheckerId')->useCurrent()->nullable();
            $table->timestamp('linguisticCheckerApprovelDate')->nullable();
            $table->unsignedBigInteger('socialMediaId')->useCurrent()->nullable();
            $table->timestamp('socialMediaApprovelDate')->nullable();
            // $table->string('image')->nullable(); // Add this line to include an image column
            $table->enum('status', ['draft', 'submitted', 'approved','reviewed','published']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
