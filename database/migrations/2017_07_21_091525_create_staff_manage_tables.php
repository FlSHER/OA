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
        // 默认密码
        $pass = '34007657c850a84fd4e2c1bb7b6b1433ca37af6c003d423d03a04ded7c13b13f';
        $marital = ['未知', '未婚', '已婚', '离异', '再婚', '丧偶'];
        $politics = [
            '未知', '群众', '中共党员', '中共预备党员', '共青团员', '民革党员', '民盟盟员',
            '民建会员','民进会员','农工党党员', '致公党党员', '九三学社社员', '台盟盟员', '无党派人士'
        ];
        $education = ['未知', '小学', '初中', '高中', '技校', '职高', '专科', '本科', '硕士', '博士'];
        $national = [
            '未知', '汉族', '蒙古族', '满族', '朝鲜族', '赫哲族', '达斡尔族', '鄂温克族', '鄂伦春族', '回族', '东乡族', '土族',
            '撒拉族', '保安族', '裕固族', '维吾尔族', '哈萨克族', '柯尔克孜族', '锡伯族', '塔吉克族', '乌孜别克族', '俄罗斯族',
            '塔塔尔族', '藏族', '门巴族', '珞巴族', '羌族', '彝族', '白族', '哈尼族', '傣族', '傈僳族', '佤族', '拉祜族',
            '纳西族', '景颇族', '布朗族', '阿昌族', '普米族', '怒族', '德昂族', '独龙族', '基诺族', '苗族', '布依族', '侗族',
            '穿青族', '仡佬族', '壮族', '瑶族', '仫佬族', '毛南族', '京族', '土家族', '黎族', '畲族', '高山族', '革家族', '水族'
        ];
        Schema::create('staff', function (Blueprint $table) use ($pass,$marital,$politics,$national,$education) {
            $table->mediumIncrements('staff_sn', true)->comment('员工编号');
            $table->char('username', 16)->default('')->comment('用户名');
            $table->char('password', 64)->default($pass)->comment('密码');
            $table->char('salt', 6)->default('000000')->comment('加密后缀');
            $table->char('realname', 10)->comment('员工姓名');
            $table->char('mobile', 11)->default('')->comment('手机号码');
            $table->enum('gender', ['未知', '男', '女'])->default('未知')->comment('性别');

            $table->unsignedSmallInteger('department_id')->default(1)->comment('部门ID');
            $table->char('shop_sn', 10)->default('')->comment('店铺编码');
            $table->unsignedSmallInteger('position_id')->default(1)->comment('职位ID');
            $table->unsignedTinyInteger('brand_id')->default(1)->comment('品牌ID');
            $table->char('dingtalk_number', 50)->default('')->comment('钉钉id');
            $table->tinyInteger('is_active')->default(1)->comment('是否激活');
            $table->tinyInteger('status_id')->default(0)->comment('状态ID');
            $table->date('hired_at')->nullable()->comment('入职日期');
            $table->date('employed_at')->nullable()->comment('转正日期');
            $table->date('left_at')->nullable()->comment('离职日期');
            $table->tinyInteger('property')->default(0)->comment('属性ID 0：无 1：108将 2：36天罡 3：24金刚 4：18罗汉');

            $table->char('id_card_number', 18)->default('')->comment('身份证号');
            $table->char('account_number', 19)->default('')->comment('银行卡号');
            $table->char('account_bank', 20)->default('')->comment('开户行');
            $table->char('account_name', 10)->default('')->comment('开户人');
            $table->tinyInteger('account_active')->default(1)->comment('是否使用工资卡 0：否，1：是');
            $table->char('recruiter_sn', 6)->default('')->comment('招聘人员工编号');
            $table->char('recruiter_name', 10)->default('')->comment('招聘人姓名');

            $table->mediumInteger('household_province_id')->default(0)->unsigned()->comment('户口所在地(省)');
            $table->mediumInteger('household_city_id')->default(0)->unsigned()->comment('户口所在地(市)');
            $table->mediumInteger('household_county_id')->default(0)->unsigned()->comment('户口所在地(区/县)');
            $table->char('household_address', 30)->default('')->comment('户口所在地(详细地址)');
            $table->mediumInteger('living_province_id')->default(0)->unsigned()->comment('现居住地(省)');
            $table->mediumInteger('living_city_id')->default(0)->unsigned()->comment('现居住地(市)');
            $table->mediumInteger('living_county_id')->default(0)->unsigned()->comment('现居住地(区/县)');
            $table->char('living_address', 30)->default('')->comment('现居住地(详细地址)');

            $table->enum('national', $national)->default('未知')->comment('民族');
            $table->enum('marital_status', $marital)->default('未知')->comment('婚姻状况');
            $table->enum('politics', $politics)->default('未知')->comment('政治面貌');
            $table->enum('education', $education)->default('未知')->comment('学历');
            $table->char('height', 3)->default('')->comment('身高');
            $table->char('weight', 3)->default('')->comment('体重');
            $table->char('native_place', 30)->default('')->comment('籍贯');
            $table->char('remark', 100)->default('')->comment('备注');
            $table->char('concat_name', 10)->default('')->comment('紧急联系人(姓名)');
            $table->char('concat_tel', 13)->default('')->comment('紧急联系人(电话)');
            $table->char('concat_type', 5)->default('')->comment('紧急联系人(关系类型)');
            $table->char('wechat_number', 20)->default('')->comment('微信号');

            $table->timestamps();
            $table->softDeletes();
            $table->index('shop_sn');
            $table->index('brand_id');
            $table->index('position_id');
            $table->index('department_id');
            $table->index('dingtalk_number');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('position_id')->references('id')->on('positions');
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
        Schema::dropIfExists('staff_status');
        Schema::dropIfExists('staff_relatives');
        Schema::dropIfExists('staff_relative_type');
    }

}
