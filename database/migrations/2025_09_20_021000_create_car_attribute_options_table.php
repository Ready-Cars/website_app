<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_attribute_options', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // category|transmission|fuel
            $table->string('value');
            $table->timestamps();
            $table->unique(['type','value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_attribute_options');
    }
};
