<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('draw_number')->unique();
            $table->string('draw_name');
            $table->json('restricted_numbers')->nullable();
            $table->integer('max_bet_amount');
            $table->integer('max_repeats_per_number');
            $table->time('closing_time');
            $table->date('draw_date');
            $table->integer('seller_margin');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_configurations');
    }
}
