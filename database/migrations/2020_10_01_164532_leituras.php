<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Leituras extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leituras', function (Blueprint $table) {
            $table->id();
            $table->decimal('leitura');
            $table->unsignedBigInteger('cod_equipamento');
            $table->foreign('cod_equipamento')->references('id')->on('equipamentos');
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
        Schema::dropIfExists('leituras');
    }
}
