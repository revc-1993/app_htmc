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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('department');
            $table->string('job_position');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('admin_since')->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('job_position_id')->nullable();
            // $table->unsignedBigInteger('department_job_position_id')->nullable();;
            $table->timestamps();

            $table->foreign('job_position_id')->references('id')->on('job_positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
