<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Pastikan tidak ada NULL sebelum dibuat NOT NULL
        DB::statement("UPDATE tower_images SET stack_no = 0 WHERE stack_no IS NULL");

        // 2) Ubah kolom jadi UNSIGNED TINYINT NOT NULL DEFAULT 0 (tanpa ->change())
        DB::statement("ALTER TABLE tower_images MODIFY stack_no TINYINT UNSIGNED NOT NULL DEFAULT 0");

        // 3) Drop unique index lama (kalau ada) tanpa Doctrine
        //    NOTE: DROP INDEX untuk UNIQUE juga pakai DROP INDEX <name> ON <table>
        try {
            DB::statement("ALTER TABLE tower_images DROP INDEX tower_images_unique");
        } catch (\Throwable $e) {
            // ignore jika tidak ada
        }

        try {
            DB::statement("ALTER TABLE tower_images DROP INDEX tower_images_tower_stack_side_unique");
        } catch (\Throwable $e) {
            // ignore jika tidak ada
        }

        // 4) Buat unique index yang benar
        //    (Kalau sudah ada, MySQL akan error, jadi kita try/catch juga)
        try {
            DB::statement("ALTER TABLE tower_images ADD UNIQUE tower_images_tower_stack_side_unique (tower_id, stack_no, side)");
        } catch (\Throwable $e) {
            // ignore jika sudah ada
        }
    }

    public function down(): void
    {
        // rollback index
        try {
            DB::statement("ALTER TABLE tower_images DROP INDEX tower_images_tower_stack_side_unique");
        } catch (\Throwable $e) {
            // ignore
        }

        // jadikan nullable lagi (opsional)
        DB::statement("ALTER TABLE tower_images MODIFY stack_no TINYINT UNSIGNED NULL");
    }
};