<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDingtalkApprovalProcessLog extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('dingtalk_approval_process', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('app_id')->comment('应用ID');
            $table->char('process_instance_id', 50)->comment('钉钉审批实例ID');
            $table->char('callback_url', 50)->comment('应用回调接收地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('dingtalk_approval_process');
    }

}
