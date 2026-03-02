<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('installation_requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('device_photo_path');
            $table->text('admin_comment')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('admin_comment');
        });
    }

    public function down(): void
    {
        Schema::table('installation_requests', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_comment', 'reviewed_at']);
        });
    }
};
