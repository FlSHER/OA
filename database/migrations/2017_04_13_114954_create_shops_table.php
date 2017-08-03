<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('shops');
        Schema::create('shops', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->char('shop_sn', 10)->comment('店铺代码');
            $table->char('name', 50)->comment('店铺名称');
            $table->char('manager_sn', 6)->comment('店长员工编号');
            $table->char('manager_name', 10)->comment('店长姓名');
            $table->smallInteger('department_id')->comment('所属部门id');
            $table->tinyInteger('brand_id')->comment('品牌id');
            $table->decimal('lng', 9, 6)->comment('经度');
            $table->decimal('lat', 9, 6)->comment('纬度');
            $table->mediumInteger('province_id');
            $table->mediumInteger('city_id');
            $table->mediumInteger('county_id');
            $table->char('address', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shops');
    }

}
