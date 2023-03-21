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
            $table->string('process_number')->nullable();
            $table->string("contract_administrator")->nullable();
            $table->date('assignment_date')->nullable();
            $table->string('japc_comments')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->integer('commitment_cur')->nullable();
            $table->float('commitment_amount')->unsigned()->nullable();
            $table->string('commitment_comments')->nullable();
            $table->date('commitment_date')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('record_status_id')->nullable();    // Estado del compromiso

            // COORDINACION GENERAL FINANCIERA
            $table->string('treasury_approved')->nullable();
            $table->string('returned_document_number')->nullable();
            $table->date('coord_cgf_date')->nullable();
            $table->string('coord_cgf_comments')->nullable();

            $table->integer('current_management')->default(2);
            $table->unsignedBigInteger("certification_id");
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('record_status_id')->references('id')->on('record_statuses');
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
