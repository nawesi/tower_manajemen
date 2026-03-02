<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // cek index yang ada di table tower_images
        $indexes = collect(DB::select("SHOW INDEX FROM tower_images"))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();

        Schema::table('tower_images', function (Blueprint $table) use ($indexes) {

            // drop index lama kalau masih ada
            if (in_array('tower_images_tower_id_side_unique', $indexes, true)) {
                $table->dropUnique('tower_images_tower_id_side_unique');
            }

            // buat index baru kalau belum ada
            if (!in_array('tower_images_tower_stack_side_unique', $indexes, true)) {
                $table->unique(['tower_id', 'stack_no', 'side'], 'tower_images_tower_stack_side_unique');
            }
        });
    }

    public function down(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM tower_images"))
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->all();

        Schema::table('tower_images', function (Blueprint $table) use ($indexes) {

            if (in_array('tower_images_tower_stack_side_unique', $indexes, true)) {
                $table->dropUnique('tower_images_tower_stack_side_unique');
            }

            if (!in_array('tower_images_tower_id_side_unique', $indexes, true)) {
                $table->unique(['tower_id', 'side'], 'tower_images_tower_id_side_unique');
            }
        });
    }
};
