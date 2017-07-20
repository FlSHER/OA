<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppTokenTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('app_token', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned();
            $table->smallInteger('app_id')->unsigned();
            $table->char('app_token', 32)->comment('访问令牌');
            $table->char('refresh_token', 32)->comment('刷新令牌');
            $table->integer('expiration')->comment('过期时间');
            $table->primary(['staff_sn', 'app_id']);
        });

        Schema::create('app_auth_code', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned();
            $table->smallInteger('app_id')->unsigned();
            $table->char('app_auth_code', 32)->comment('授权码');
            $table->char('redirect_uri', 50)->comment('重定向地址');
            $table->integer('expiration')->comment('过期时间');
            $table->primary(['staff_sn', 'app_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('app_token');
        Schema::dropIfExists('app_auth_code');
    }

}
