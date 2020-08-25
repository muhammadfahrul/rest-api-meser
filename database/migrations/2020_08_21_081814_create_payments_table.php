<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('transaction_id');
            $table->string('payment_type');
            $table->string('gross_amount');
            $table->string('bank');
            $table->string('transaction_time');
            $table->string('transaction_status');
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
        Schema::dropIfExists('t_payments');
    }
}
