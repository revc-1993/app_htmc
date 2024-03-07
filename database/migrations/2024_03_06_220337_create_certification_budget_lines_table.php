<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certification_budget_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certification_id');
            $table->unsignedBigInteger('budget_line_id');
            $table->timestamps();

            $table->foreign('certification_id')->references('id')->on('certifications');
            $table->foreign('budget_line_id')->references('id')->on('budget_lines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certification_budget_lines');
    }
};
