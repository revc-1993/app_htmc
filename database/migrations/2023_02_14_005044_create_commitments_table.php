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
            $table->unsignedBigInteger("contract_administrator_id")->nullable();
            $table->string("purchase_order")->nullable();
            $table->timestamp('sec_cgf_date')->nullable();
            $table->string("contract_number")->nullable();
            $table->string('sec_cgf_comments')->nullable();

            $table->timestamp('assignment_date')->nullable();
            $table->string('japc_comments')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->integer('commitment_cur')->nullable();
            $table->float('commitment_amount')->unsigned()->nullable();
            $table->float('balance')->unsigned()->nullable();
            $table->string('commitment_comments')->nullable();
            $table->timestamp('commitment_date')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('record_status_id')->nullable();    // Estado del compromiso

            // COORDINACION GENERAL FINANCIERA
            $table->string('treasury_approved')->nullable();
            $table->string('returned_document_number')->nullable();
            $table->timestamp('coord_cgf_date')->nullable();
            $table->string('coord_cgf_comments')->nullable();

            $table->unsignedBigInteger('current_management')->default(2);
            $table->unsignedBigInteger("certification_id")->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('contract_administrator_id')->references('id')->on('contract_administrators');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('record_status_id')->references('id')->on('record_statuses');
            $table->foreign('certification_id')->references('id')->on('certifications');
            $table->foreign('current_management')->references('id')->on('current_management');
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
