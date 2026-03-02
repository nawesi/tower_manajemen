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
public function up(): void
{
    Schema::create('installation_requests', function (Blueprint $table) {
        $table->id();

        $table->foreignId('tower_id')->constrained()->cascadeOnDelete();

        $table->string('vendor_department');
        $table->string('device_name');

        $table->unsignedTinyInteger('stack_no'); // 1..7
        $table->decimal('height_from_ground_m', 8, 2)->nullable(); // meter

        $table->string('device_photo_path')->nullable(); // storage path

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installation_requests');
    }
};
