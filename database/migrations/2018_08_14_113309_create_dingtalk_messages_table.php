<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDingtalkMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dingtalk_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('client_id')->nullable()->comment('客服端授权应用ID')->index();
            $table->unsignedInteger('agent_id')->comment('钉钉应用ID')->index();
            $table->unsignedInteger('create_staff')->comment('创建人工号')->index();
            $table->char('create_realname',20)->comment('创建人');
            $table->char('msgtype',10)->comment('消息请求类型，text，image,file,link,markdown,oa,action_card');
            $table->text('data')->comment('请求data数据');
            $table->unsignedInteger('step_run_id')->nullable()->comment('步骤运行ID');
            $table->char('errcode',20)->nullable()->commont('发送状态， 0 成功')->index();
            $table->char('task_id',100)->nullable()->comment('创建的发送任务id');
            $table->char('request_id',100)->nullable()->comment('返回request的ID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dingtalk_messages');
    }
}
