<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('tower_images', function (Blueprint $table) {
        // drop hanya kalau ada (supaya tidak error)
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('tower_images');

        if (array_key_exists('tower_images_unique', $indexes)) {
            $table->dropUnique('tower_images_unique');
        }
    });
}

public function down()
{
    Schema::table('tower_images', function (Blueprint $table) {
        $table->unique(['tower_id','stack_no','side'], 'tower_images_unique');
    });
}

};
