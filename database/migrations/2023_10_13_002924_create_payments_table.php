<?php

use App\Constants\ManagementRecordStatus;
use App\Constants\ManagementRoles;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accrual_id');

            $table->unsignedBigInteger('customer_id')->nullable();

            $table->unsignedBigInteger('manager_status')->default(ManagementRecordStatus::NOT_REVIEWED);
            $table->string('treasury_approved')->nullable();
            $table->string('manager_comments')->nullable();
            $table->timestamp('manager_date')->nullable();

            $table->unsignedBigInteger('analyst_status')->nullable();
            $table->string('analyst_comments')->nullable();
            $table->timestamp('analyst_date')->nullable();

            $table->unsignedBigInteger('current_management')->default(ManagementRoles::SEC_CGF);

            $table->timestamps();

            $table->foreign('accrual_id')->references('id')->on('accruals');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('manager_status')->references('id')->on('record_statuses');
            $table->foreign('analyst_status')->references('id')->on('record_statuses');
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
        Schema::dropIfExists('payments');
    }
};
