<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppInfoToOauthClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->char('icon', 50)->comment('图标文件')->default('');
            $table->char('url', 50)->comment('访问地址')->default('');
            $table->char('logout_url', 50)->comment('退出登录地址')->default('');
            $table->tinyInteger('is_active')->default(0)->comment('是否启用 1：是，0：否')->default(1);
            $table->mediumInteger('authority_id')->comment('关联权限id')->default(0);
            $table->integer('agent_id')->comment('钉钉应用id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropIfExists('icon');
            $table->dropIfExists('url');
            $table->dropIfExists('logout_url');
            $table->dropIfExists('is_active');
            $table->dropIfExists('authority_id');
            $table->dropIfExists('agent_id');
        });
    }
}
