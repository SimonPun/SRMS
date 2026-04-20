<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_request_staff', function (Blueprint $table) {
            $table->string('staff_status')->default('pending')->after('user_id');
        });

        DB::table('service_request_staff')
            ->whereNull('staff_status')
            ->update(['staff_status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_request_staff', function (Blueprint $table) {
            $table->dropColumn('staff_status');
        });
    }
};
