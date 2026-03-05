<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vendor_name')->nullable()->after('name');
            $table->string('pic_name')->nullable()->after('vendor_name');
            $table->string('phone')->nullable()->after('pic_name');

            // jangan pakai change() kalau belum perlu (menghindari DBAL issues)
            // $table->string('email')->nullable()->change();

            $table->string('task_desc', 255)->nullable()->after('email');
            $table->timestamp('access_expires_at')->nullable()->after('task_desc');
            $table->enum('account_status', ['active', 'inactive'])->default('active')->after('access_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'vendor_name',
                'pic_name',
                'phone',
                'task_desc',
                'access_expires_at',
                'account_status',
            ]);
        });
    }
}