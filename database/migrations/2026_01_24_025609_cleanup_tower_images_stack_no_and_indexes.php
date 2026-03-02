<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Pastikan stack_no tidak NULL
        DB::statement("UPDATE tower_images SET stack_no = 0 WHERE stack_no IS NULL");

        // 2) Set stack_no jadi NOT NULL default 0 (tanpa ->change())
        DB::statement("ALTER TABLE tower_images MODIFY stack_no TINYINT UNSIGNED NOT NULL DEFAULT 0");

        // 3) Drop index yang redundant (kalau ada)
        //    (DROP INDEX works untuk index biasa & unique)
        $dropCandidates = [
            'tower_images_unique',
            'tower_images_tower_stack_side_unique',
            'tower_images_tower_id_side_unique',
        ];

        foreach ($dropCandidates as $idx) {
            try {
                DB::statement("ALTER TABLE tower_images DROP INDEX {$idx}");
            } catch (\Throwable $e) {
                // ignore jika index tidak ada
            }
        }

        // 4) Pastikan hanya ada 1 unique index yang benar
        try {
            DB::statement("ALTER TABLE tower_images ADD UNIQUE tower_images_tower_stack_side_unique (tower_id, stack_no, side)");
        } catch (\Throwable $e) {
            // ignore jika sudah ada
        }
    }

    public function down(): void
    {
        // rollback minimal: hapus unique yang kita buat
        try {
            DB::statement("ALTER TABLE tower_images DROP INDEX tower_images_tower_stack_side_unique");
        } catch (\Throwable $e) {
            // ignore
        }

        // kembalikan nullable (opsional)
        DB::statement("ALTER TABLE tower_images MODIFY stack_no TINYINT UNSIGNED NULL");
    }
};