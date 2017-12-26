<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppManageTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('name', 20)->comment('应用名称');
            $table->char('pic_path', 50)->comment('图标文件');
            $table->char('url', 50)->comment('访问地址');
            $table->char('logout_url', 50)->comment('退出登录地址');
            $table->char('client_ip', 20)->comment('应用ip地址');
            $table->tinyInteger('is_active')->default(0)->comment('是否启用 1：是，0：否');
            $table->char('app_ticket', 32)->comment('应用密钥');
            $table->mediumInteger('authority_id')->comment('关联权限id');
            $table->integer('agent_id')->comment('钉钉应用id');
        });
        Schema::create('app_token', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned();
            $table->smallInteger('app_id')->unsigned();
            $table->char('app_token', 32)->comment('访问令牌');
            $table->char('refresh_token', 32)->comment('刷新令牌');
            $table->integer('expiration')->comment('过期时间');
            $table->primary(['staff_sn', 'app_id']);
            $table->index(['staff_sn', 'app_id']);
        });

        Schema::create('app_auth_code', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned();
            $table->smallInteger('app_id')->unsigned();
            $table->char('app_auth_code', 32)->comment('授权码');
            $table->char('redirect_uri', 100)->comment('重定向地址');
            $table->integer('expiration')->comment('过期时间');
            $table->primary(['staff_sn', 'app_id']);
            $table->index(['staff_sn', 'app_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apps');
        Schema::dropIfExists('app_token');
        Schema::dropIfExists('app_auth_code');
    }

}
