<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetsTable extends Migration
{
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bet_id')->index();
            $table->unsignedBigInteger('draw_number');
            $table->unsignedBigInteger('bet_number');
            $table->integer('bet_amount');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('associated_id');
            $table->timestamp('bet_date_time');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('associated_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bets');
    }
}

