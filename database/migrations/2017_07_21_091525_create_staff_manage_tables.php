<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffManageTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 员工主表
         */
        Schema::create('staff', function (Blueprint $table) {
            $table->mediumIncrements('staff_sn', true)->comment('员工编号');
            $table->char('username', 16)->comment('用户名');
            $table->char('password', 64)->comment('密码');
            $table->char('salt', 6)->comment('加密后缀');
            $table->char('realname', 10)->comment('员工姓名');
            $table->char('mobile', 11)->comment('手机号码');
            $table->char('wechat_number', 20)->comment('微信号');
            $table->char('dingtalk_number', 50)->comment('钉钉id');
            $table->char('id_card_number', 18)->comment('身份证号');
            $table->unsignedTinyInteger('gender_id')->comment('性别ID');
            $table->date('birthday')->comment('生日');
            $table->unsignedTinyInteger('brand_id')->comment('品牌ID');
            $table->unsignedSmallInteger('department_id')->comment('部门ID');
            $table->unsignedSmallInteger('position_id')->comment('职位ID');
            $table->char('shop_sn', 10)->comment('店铺编码');
            $table->tinyInteger('status_id')->comment('状态ID');
            $table->unsignedTinyInteger('is_active')->comment('是否激活');
            $table->unsignedTinyInteger('property_id')->comment('属性ID 0：无 1：108将 2：36天罡 3：24金刚 4：18罗汉');
            $table->date('hired_at')->comment('入职日期');
            $table->date('employed_at')->comment('转正日期');
            $table->date('left_at')->comment('离职日期');
            $table->mediumInteger('household_province_id')->unsigned()->comment('户口所在地(省)');
            $table->mediumInteger('household_city_id')->unsigned()->comment('户口所在地(市)');
            $table->mediumInteger('household_county_id')->unsigned()->comment('户口所在地(区/县)');
            $table->char('household_address', 30)->comment('户口所在地(详细地址)');
            $table->mediumInteger('living_province_id')->unsigned()->comment('现居住地(省)');
            $table->mediumInteger('living_city_id')->unsigned()->comment('现居住地(市)');
            $table->mediumInteger('living_county_id')->unsigned()->comment('现居住地(区/县)');
            $table->char('living_address', 30)->comment('现居住地(详细地址)');
            $table->timestamps();
            $table->rememberToken();
            $table->softDeletes();
            $table->foreign('brand')->references('id')->on('brands');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->foreign('shop_sn')->references('shop_sn')->on('shops');
            $table->foreign('status_id')->references('id')->on('staff_status');
            $table->foreign('property_id')->references('id')->on('staff_property');
        });
        /**
         * 员工详细信息表
         */
        Schema::create('staff_info', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned()->primary();
            $table->char('account_number', 19)->comment('银行卡号');
            $table->char('account_bank', 20)->comment('开户行');
            $table->char('account_name', 10)->comment('开户人');
            $table->char('email', 40)->comment('电子邮箱');
            $table->char('qq_number', 11)->comment('QQ号码');
            $table->mediumInteger('recruiter_sn')->unsigned()->comment('招聘人员工编号');
            $table->char('recruiter_name', 10)->comment('招聘人姓名');
            $table->char('height', 3)->comment('身高');
            $table->char('weight', 3)->comment('体重');
            $table->char('national', 10)->comment('民族');
            $table->char('marital_status', 10)->comment('婚姻状况');
            $table->char('politics', 10)->comment('政治面貌');
            $table->char('native_place', 30)->comment('籍贯');
            $table->char('education', 5)->comment('学历');
            $table->char('remark', 200)->comment('备注');
            $table->char('concat_name', 10)->comment('紧急联系人(姓名)');
            $table->char('concat_tel', 13)->comment('紧急联系人(电话)');
            $table->char('concat_type', 5)->comment('紧急联系人(关系类型)');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * 员工状态
         */
        Schema::create('staff_status', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('name', 10)->comment('状态名称');
            $table->unsignedTinyInteger('sort')->comment('排序');
        });

        /**
         * 员工属性
         */
        Schema::create('staff_property', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('name', 10)->comment('属性名称');
            $table->unsignedTinyInteger('sort')->comment('排序');
        });

        /**
         * 关系人表
         */
        Schema::create('staff_relatives', function (Blueprint $table) {
            $table->mediumInteger('staff_sn')->unsigned()->comment('员工编号');
            $table->mediumInteger('relative_sn')->unsigned()->comment('关联员工编号');
            $table->char('relative_name', 10)->comment('关联员工姓名');
            $table->tinyInteger('relative_type')->unsigned()->comment('关系类型');
            $table->timestamps();
            $table->primary(['staff_sn', 'relative_sn']);
        });
        /**
         * 关系类型表
         */
        Schema::create('staff_relative_type', function (Blueprint $table) {
            $table->tinyIncrements('id', true)->unsigned();
            $table->char('name', 5);
            $table->tinyInteger('group_id')->unsigned()->comment('关系分组');
            $table->tinyInteger('opposite_group_id')->unsigned()->comment('对立分组');
            $table->tinyInteger('gender_id')->unsigned()->default(0)->comment('性别限制');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('排序');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff');
        Schema::dropIfExists('staff_info');
        Schema::dropIfExists('staff_status');
        Schema::dropIfExists('staff_relatives');
        Schema::dropIfExists('staff_relative_type');
    }

}
