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
        Schema::create('dogs', function (Blueprint $table) {
            $table->id();
            $table->string('dog_name');
            $table->foreignId('breed_id')->constrained()->cascadeOnDelete();
            $table->string('age');
            $table->enum('dog_size', ['small', 'medium', 'large']);
            $table->string('dog_color');
            $table->string('dog_img');
            $table->text('dog_description');
            $table->boolean('is_adopted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dogs');
    }
};
