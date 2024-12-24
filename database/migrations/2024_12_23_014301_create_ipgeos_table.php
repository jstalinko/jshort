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
        Schema::create('ipgeos', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('country_name');
            $table->string('country_code');
            $table->string('region_code');
            $table->string('region_name');
            $table->string('city');
            $table->string('zip')->nullable();
            $table->string('isp')->nullable();
            $table->string('lon')->nullable();
            $table->string('lat')->nullable();
            $table->boolean('is_proxy')->default(false);
            $table->boolean('is_hosting')->default(false);
            $table->string('by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipgeos');
    }
};
