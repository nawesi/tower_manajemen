<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('tower_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tower_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('stack_no')->default(0); // 0 = umum / non-stack
    $table->unsignedTinyInteger('side'); // 1..4
    $table->string('image_path');
    $table->timestamps();

    $table->unique(['tower_id', 'stack_no', 'side'], 'tower_images_tower_stack_side_unique');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tower_images');
    }
};
