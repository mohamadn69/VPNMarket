<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('ms_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('flag')->nullable();
            $table->string('slug')->unique(); // germany
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        Schema::create('ms_servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('ms_locations')->cascadeOnDelete();

            $table->string('name');
            $table->string('ip_address');
            $table->integer('port')->default(54321);
            $table->string('username');
            $table->string('password');
            $table->boolean('is_https')->default(false);

            $table->string('path')->default('/');
            $table->integer('inbound_id');

            $table->integer('capacity')->default(1000);
            $table->integer('current_users')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });



        if (!Schema::hasColumn('orders', 'server_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('server_id')->nullable()->after('plan_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ms_servers');
        Schema::dropIfExists('ms_locations');
    }
};
