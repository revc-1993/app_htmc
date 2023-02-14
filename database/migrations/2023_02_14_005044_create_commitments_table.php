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
            $table->string("process_code");
            $table->string("vendor_name");
            $table->string("contract_administrator")->nullable();
            $table->string("amount_to_commit");
            $table->string('management_status')->default("Pendiente de revisión");  // Considerar posibilidad de FK a tabla ESTADOS
            $table->unsignedBigInteger("certification_id");
            $table->softDeletes();
            $table->timestamps();

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
