<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDigemidAyudaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digemid_ayuda', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cod_prod', 100)->nullable();
            $table->text('nom_prod')->nullable();
            $table->text('concent')->nullable();
            $table->text('nom_form_farm')->nullable();
            $table->text('presentac')->nullable();
            $table->text('fraccion')->nullable();
            $table->text('num_reg_san')->nullable();
            $table->text('nom_titular')->nullable();
            $table->text('nom_fabricante')->nullable();
            $table->text('nom_ifa')->nullable();
            $table->text('nom_rubro')->nullable();
            $table->text('situacion')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('digemid_ayuda');
    }
}
