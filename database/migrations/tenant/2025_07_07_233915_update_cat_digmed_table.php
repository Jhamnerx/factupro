<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCatDigmedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cat_digemid', function (Blueprint $table) {
            $table->dropColumn('nom_form_farm_simplif');
            $table->text('nom_fabricante')->nullable()->after('nom_titular');
            $table->text('nom_ifa')->nullable()->after('nom_fabricante');
            $table->text('nom_rubro')->nullable()->after('nom_ifa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
