<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tower_images', function (Blueprint $table) {
            if (!Schema::hasColumn('tower_images', 'stack_no')) {
                $table->unsignedTinyInteger('stack_no')->nullable()->after('tower_id');
            }

            // pastikan kombinasi tower+stack+side unik
            $table->unique(['tower_id', 'stack_no', 'side'], 'tower_images_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tower_images', function (Blueprint $table) {
            $table->dropUnique('tower_images_unique');
            $table->dropColumn('stack_no');
        });
    }
};
