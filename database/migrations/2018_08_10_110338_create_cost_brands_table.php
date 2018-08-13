<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_brands', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('name', 10)->comment('名称');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('brand_has_cost_brands', function (Blueprint $table) {
            $table->unsignedTinyInteger('brand_id')->comment('品牌ID');
            $table->unsignedTinyInteger('cost_brand_id')->comment('费用品牌ID');
            $table->index(['brand_id', 'cost_brand_id']);
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('cost_brand_id')->references('id')->on('cost_brands');
        });

        Schema::create('staff_has_cost_brands', function (Blueprint $table) {
            $table->unsignedMediumInteger('staff_sn')->comment('员工编号');
            $table->unsignedTinyInteger('cost_brand_id')->comment('费用品牌ID');
            $table->index(['staff_sn', 'cost_brand_id']);
            $table->foreign('staff_sn')->references('staff_sn')->on('staff');
            $table->foreign('cost_brand_id')->references('id')->on('cost_brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_has_cost_brands');
        Schema::dropIfExists('brand_has_cost_brands');
        Schema::dropIfExists('cost_brands');
    }
}
