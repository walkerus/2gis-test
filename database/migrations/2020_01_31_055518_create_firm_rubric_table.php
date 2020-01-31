<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmRubricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firm_rubric', function (Blueprint $table) {
            $table->integer('firm_id');
            $table->integer('rubric_id');
            $table->timestamps();
            $table->foreign('firm_id')->references('id')->on('firms');
            $table->foreign('rubric_id')->references('id')->on('rubrics');
            $table->index('rubric_id');
            $table->unique(['firm_id', 'rubric_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firm_rubric');
    }
}
