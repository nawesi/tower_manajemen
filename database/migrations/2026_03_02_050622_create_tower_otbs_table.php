<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tower_otbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_id')->constrained('towers')->cascadeOnDelete();
            $table->string('name')->default('OTB 1');
            $table->unsignedSmallInteger('total_ports')->default(12);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tower_otbs');
    }
};