<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ms_servers', function (Blueprint $table) {

            if (!Schema::hasColumn('ms_servers', 'link_type')) {
                $table->string('link_type')->default('single')->after('is_active');
            }



            if (!Schema::hasColumn('ms_servers', 'tunnel_address')) {
                $table->string('tunnel_address')->nullable()->after('subscription_port');
            }

            if (!Schema::hasColumn('ms_servers', 'tunnel_port')) {
                $table->unsignedInteger('tunnel_port')->nullable()->default(443)->after('tunnel_address');
            }

            if (!Schema::hasColumn('ms_servers', 'tunnel_is_https')) {
                $table->boolean('tunnel_is_https')->default(true)->after('tunnel_port');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ms_servers', function (Blueprint $table) {
            $table->dropColumn(['link_type', 'tunnel_address', 'tunnel_port', 'tunnel_is_https']);
        });
    }
};
