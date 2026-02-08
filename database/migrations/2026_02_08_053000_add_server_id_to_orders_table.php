<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // اضافه کردن ستون server_id برای پشتیبانی از MultiServer
            $table->unsignedBigInteger('server_id')->nullable()->after('plan_id');
            
            // اگر جدول ms_servers وجود دارد، foreign key اضافه کن
            if (Schema::hasTable('ms_servers')) {
                $table->foreign('server_id')
                    ->references('id')
                    ->on('ms_servers')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // حذف foreign key اگر وجود دارد
            if (Schema::hasTable('ms_servers')) {
                $table->dropForeign(['server_id']);
            }
            
            $table->dropColumn('server_id');
        });
    }
};
