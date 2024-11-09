<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawNumberWinnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draw_number_winner', function (Blueprint $table) {
            $table->id();
            $table->integer('draw_number');
            $table->integer('winning_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('draw_number_winner');
    }
}
