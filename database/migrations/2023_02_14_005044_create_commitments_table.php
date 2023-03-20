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
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            //Buscar certificación
            $table->string('commitment_memo')->nullable();
            $table->string("contract_administrator")->nullable();
            $table->date('assignment_date')->nullable();

            $table->date('commitment_date')->nullable();
            $table->string("nit_name")->nullable();
            $table->float('commitment_amount')->unsigned()->nullable();
            $table->string('commitment_comments')->nullable();


            $table->integer('current_management')->default(2);
            $table->string('record_status_id')->default();
            $table->unsignedBigInteger("certification_id");
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('certification_id')->references('id')->on('certifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commitments');
    }
};
