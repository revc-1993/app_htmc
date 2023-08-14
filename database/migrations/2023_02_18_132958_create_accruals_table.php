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
        Schema::create('accruals', function (Blueprint $table) {
            $table->id();
            $table->string('accrual_memo')->nullable();
            $table->timestamp('assignment_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('japc_comments')->nullable();

            $table->unsignedBigInteger("commitment_id")->nullable();
            $table->integer('accrual_cur')->nullable();
            $table->timestamp('accrual_date')->nullable();
            $table->unsignedBigInteger('record_status_id')->nullable();
            $table->float('accrual_amount')->unsigned()->nullable();
            $table->string('accrual_comments')->nullable();

            // COORDINACION GENERAL FINANCIERA
            $table->string('treasury_approved')->nullable();
            $table->string('returned_document_number')->nullable();
            $table->timestamp('coord_cgf_date')->nullable();
            $table->string('coord_cgf_comments')->nullable();

            $table->integer('current_management')->default(2);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('record_status_id')->references('id')->on('record_statuses');
            $table->foreign('commitment_id')->references('id')->on('commitments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accruals');
    }
};
