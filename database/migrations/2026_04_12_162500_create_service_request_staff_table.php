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
        Schema::create('service_request_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['service_request_id', 'user_id']);
        });

        $legacyAssignments = DB::table('service_requests')
            ->whereNotNull('assigned_to')
            ->select('id', 'assigned_to')
            ->get();

        foreach ($legacyAssignments as $assignment) {
            DB::table('service_request_staff')->insert([
                'service_request_id' => $assignment->id,
                'user_id' => $assignment->assigned_to,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_staff');
    }
};
