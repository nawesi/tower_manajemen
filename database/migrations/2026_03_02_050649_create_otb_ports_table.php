<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otb_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_otb_id')->constrained('tower_otbs')->cascadeOnDelete();
            $table->unsignedSmallInteger('port_no');
            $table->enum('status', ['ready', 'used', 'broken'])->default('ready');
            $table->string('photo_path')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['tower_otb_id', 'port_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otb_ports');
    }
};