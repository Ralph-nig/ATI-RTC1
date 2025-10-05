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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_create')->default(false)->after('status');
            $table->boolean('can_read')->default(true)->after('can_create');
            $table->boolean('can_update')->default(false)->after('can_read');
            $table->boolean('can_delete')->default(false)->after('can_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_create', 'can_read', 'can_update', 'can_delete']);
        });
    }
};