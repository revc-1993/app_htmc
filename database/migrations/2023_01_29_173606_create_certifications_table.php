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
            // Prefijos -> cf: coordinación financiera. an: analista.
            $table->id();
            // COORDINACION FINANCIERA
            $table->string('contract_object');
            $table->string('requesting_area');  // Este debe ser FK a tabla AREAS - FUNCIONES
            $table->date('reception_date');
            $table->float('amount')->unsigned();
            // ANALISTA FINANCIERA
            $table->date('assignment_date')->nullable();
            $table->date('japc_reassignment_date')->nullable();
            $table->string('budget_line')->nullable();  // Debe ser FK a tabla PARTIDAS PRESUPUESTARIAS
            $table->string('process_id')->nullable();  // Considerar posibilidad de FK a tabla PROCESOS
            $table->string('certification_number')->nullable();  // Actualiza Analista y Secretaria
            $table->float('amount_to_commit')->unsigned()->nullable();
            $table->string('obligation_type')->nullable();
            $table->string('process_type')->nullable();
            $table->string('comments')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();  // Considerar posibilidad de FK a tabla USERS
            $table->string('returned_document_number')->nullable();
            $table->string('management_status')->default("Pendiente de revisión");  // Considerar posibilidad de FK a tabla ESTADOS
            $table->softDeletes();
            $table->timestamps();
            // Relaciones
            $table->foreign('customer_id')->references('id')->on('users');
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
