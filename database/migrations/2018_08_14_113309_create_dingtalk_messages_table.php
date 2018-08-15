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
            $table->unsignedInteger('client_id')->nullable()->comment('客服端授权应用ID');
            $table->unsignedInteger('agent_id')->comment('钉钉应用ID');
            $table->unsignedInteger('create_staff')->comment('创建人工号');
            $table->char('create_realname',20)->comment('创建人');
            $table->text('userid_list')->nullable()->comment('钉钉接收者员工ID');
            $table->text('dept_id_list')->nullable()->comment('钉钉接收者部门ID');
            $table->unsignedTinyInteger('to_all_user')->default(0)->comment('发给钉钉全部用户 1是，0否');
            $table->unsignedTinyInteger('errcode')->commont('发送状态， 0 成功');
            $table->char('task_id',100)->nullable()->comment('创建的发送任务id');
            $table->char('request_id',100)->comment('返回request的ID');
            $table->char('msgtype',10)->comment('消息请求类型，text，image,file,link,markdown,oa,action_card');
            $table->text('data')->comment('请求msg数据');
            $table->index('client_id');
            $table->index('agent_id');
            $table->index('create_staff');
            $table->index('errcode');
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
