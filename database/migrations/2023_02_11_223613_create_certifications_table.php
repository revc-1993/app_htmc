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
            $table->string('process_type');
            $table->string('contract_object');
            $table->string('expense_type');
            $table->string('cgf_comments')->nullable();
            $table->date('cgf_date');
            $table->unsignedBigInteger('department_id')->nullable();

            // SECRETARÍA JAPC-CP
            $table->date('assignment_date')->nullable();
            $table->string('japc_comments')->nullable();

            // ANALISTA DE CERTIFICACIÓN
            $table->string('process_number')->nullable();
            $table->string('nit_name')->nullable();
            $table->date('cp_date')->nullable();
            $table->string('budget_line')->nullable();
            $table->float('certified_amount')->unsigned()->nullable();
            $table->string('certification_status')->nullable();
            $table->string('certification_comments')->nullable();

            // TESORERÍA
            $table->string('treasury_approved')->nullable();
            $table->string('returned_document_number')->nullable();

            // CONTROL TOTAL
            $table->unsignedBigInteger('record_status')->default(1);    // Roles
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // RELACIONES
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('department_id')->references('id')->on('departments');
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
