<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            if (Schema::hasColumn('service_types', 'default_price')) {
                $table->dropColumn('default_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            if (! Schema::hasColumn('service_types', 'default_price')) {
                $table->decimal('default_price', 10, 2)->nullable();
            }
        });
    }
};
