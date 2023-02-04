<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_transactions', function (Blueprint $table) {
            $table->id();
            $table->text('noted')->nullable();
            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->unsignedBigInteger('invoice_id')->index();
            // $table->foreign('invoice_id')->references('id')->on('invoice')->onDelete('cascade');
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
        Schema::dropIfExists('table_transactions');
    }
}
