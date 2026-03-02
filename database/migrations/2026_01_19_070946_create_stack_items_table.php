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
        Schema::create('stack_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tower_id')->constrained()->cascadeOnDelete();
        $table->unsignedTinyInteger('stack_no'); // 1..7
        $table->string('device_name');
        $table->timestamps();

        $table->index(['tower_id', 'stack_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stack_items');
    }
};
