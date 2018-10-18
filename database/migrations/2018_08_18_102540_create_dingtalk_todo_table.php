<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDingtalkTodoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dingtalk_todos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('create_staff')->comment('创建人工号');
            $table->char('create_realname',20)->comment('创建人');
            $table->unsignedInteger('todo_staff')->comment('待办人工号');
            $table->char('todo_userid',100)->comment('待办人钉钉号');
            $table->unsignedBigInteger('create_time')->comment('待办时间。Unix时间戳，毫秒级');
            $table->string('title')->comment('待办事项的标题');
            $table->string('url')->comment('待办事项的跳转链接');
            $table->text('form_item_list')->comment('待办事项表单 title表单标题、content表单内容');
            $table->text('data')->comment('待办提交的data数据');
            $table->tinyInteger('errcode')->comment('返回码 0 成功');
            $table->string('errmsg')->nullable()->comment('对返回码的文本描述内容');
            $table->string('record_id')->nullable()->comment('待办事项唯一id，更新待办事项的时候需要用到');
            $table->string('request_id')->nullable()->comment('request_id');
            $table->unsignedInteger('step_run_id')->default(0)->comment('步骤运行ID');
            $table->unsignedTinyInteger('is_finish')->default(0)->comment('是否审批通过 1是，0否');
            $table->index('create_staff');
            $table->index('todo_staff');
            $table->index('todo_userid');
            $table->index('create_time');
            $table->index('record_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dingtalk_todos');
    }
}
