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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();

            // SECRETARÍA COORDINACION FINANCIERA
            $table->string('certification_memo')->nullable();
            $table->string('content')->nullable();
            $table->string('contract_object');
            $table->unsignedBigInteger('process_type_id')->nullable();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('sec_cgf_comments')->nullable();
            $table->timestamp('sec_cgf_date');

            // SECRETARÍA JAPC-CP
            $table->timestamp('assignment_date')->nullable();
            $table->string('japc_comments')->nullable();

            // ANALISTA DE CERTIFICACIÓN
            $table->float('certified_amount')->unsigned()->nullable();
            $table->float('balance')->unsigned()->nullable();
            $table->integer('certification_number')->nullable();
            $table->timestamp('cp_date')->nullable();
            $table->string('certification_comments')->nullable();

            // COORDINACION GENERAL FINANCIERA
            $table->string('treasury_approved')->nullable();
            $table->string('returned_document_number')->nullable();
            $table->timestamp('coord_cgf_date')->nullable();
            $table->string('coord_cgf_comments')->nullable();

            // CONTROL TOTAL
            $table->unsignedBigInteger('current_management')->default(2);
            $table->unsignedBigInteger('record_status_id')->nullable();    // Estado de la certificación
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // RELACIONES
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('process_type_id')->references('id')->on('process_types');
            $table->foreign('expense_type_id')->references('id')->on('expense_types');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('record_status_id')->references('id')->on('record_statuses');
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
        Schema::dropIfExists('certifications');
    }
};
