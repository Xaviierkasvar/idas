<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_tracking', function (Blueprint $table) {
            $table->id(); // Crea un campo 'id' autoincremental
            $table->unsignedBigInteger('user_id'); // ID del usuario que realiza la apuesta
            $table->unsignedBigInteger('associated_id')->nullable(); // ID asociado, puede ser nulo
            $table->unsignedBigInteger('bet_id')->nullable(); // Relación con la ID de la apuesta en la tabla de apuestas
            $table->integer('draw_number'); // Número del sorteo
            $table->integer('bet_amount'); // Monto apostado
            $table->integer('payout_amount')->default(0); // Monto pagado al usuario en caso de ganancia
            $table->enum('transaction_type', ['Bet', 'Payout']); // Tipo de transacción: Apuesta o Pago
            $table->integer('balance'); // Balance actual después de la transacción
            $table->timestamp('transaction_date');
            $table->timestamps();
            
            // Definición de las claves foráneas
            $table->foreign('bet_id')->references('bet_id')->on('bets')->onDelete('cascade'); // Relación con la tabla de apuestas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla de usuarios
            $table->foreign('associated_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla de usuarios
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_tracking');
    }
}
