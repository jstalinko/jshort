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
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('short')->unique();
            $table->string('real_url');
            $table->string('cloak_url');
            $table->integer('total_allowed')->default(0);
            $table->integer('total_blocked')->default(0);

            /** FEATURE */
            $table->boolean('block_vpn')->default(false);
            $table->boolean('block_crawler')->default(false);
            $table->boolean('logs')->default(true);
            $table->text('block_isp')->nullable();
            $table->text('block_ip')->nullable();
            

            $table->string('lock_country')->default('all');
            $table->string('lock_browser')->default('all');
            $table->string('lock_device')->default('all');
            $table->string('lock_os')->default('all');
            $table->string('lock_referer')->default('all');
            
            $table->integer('throttle')->default(10);
            $table->enum('method' , ['header','js','meta'])->default('header');

            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortlinks');
    }
};
