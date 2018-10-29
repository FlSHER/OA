<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopManageTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('shop_sn', 10)->comment('店铺代码');
            $table->char('name', 50)->comment('店铺名称');
            $table->char('ip_addr', 15)->comment('店铺访问ip');
            $table->char('password', 64)->comment('店铺密码');
            $table->char('salt', 6)->comment('店铺名称');
            $table->smallInteger('department_id')->unsigned()->comment('所属部门id');
            $table->tinyInteger('brand_id')->unsigned()->comment('品牌id');
            $table->decimal('lng', 9, 6)->nullable()->comment('经度');
            $table->decimal('lat', 9, 6)->nullable()->comment('纬度');
            $table->mediumInteger('province_id')->comment('省');
            $table->mediumInteger('city_id')->comment('市');
            $table->mediumInteger('county_id')->comment('区/县');
            $table->char('address', 50)->default('')->comment('详细地址');
            $table->time('clock_in')->default('09:00:00')->comment('上班时间');
            $table->time('clock_out')->default('21:00:00')->comment('下班时间');
            $table->char('geo_hash', 20)->default('')->comment('地理位置范围');

            $table->timestamps();
            $table->softDeletes();
            $table->unique('shop_sn');
            $table->index('brand_id');
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }

}
