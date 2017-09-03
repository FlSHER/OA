<?php

use Illuminate\Database\Seeder;

class StaffRelativeTypeSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            ['name' => '父亲', 'group_id' => 1, 'opposite_group_id' => 2, 'gender_id' => 1, 'sort' => 1],
            ['name' => '母亲', 'group_id' => 1, 'opposite_group_id' => 2, 'gender_id' => 2, 'sort' => 2],
            ['name' => '儿子', 'group_id' => 2, 'opposite_group_id' => 1, 'gender_id' => 1, 'sort' => 3],
            ['name' => '女儿', 'group_id' => 2, 'opposite_group_id' => 1, 'gender_id' => 2, 'sort' => 4],
            ['name' => '哥哥', 'group_id' => 3, 'opposite_group_id' => 4, 'gender_id' => 1, 'sort' => 5],
            ['name' => '姐姐', 'group_id' => 3, 'opposite_group_id' => 4, 'gender_id' => 2, 'sort' => 6],
            ['name' => '弟弟', 'group_id' => 4, 'opposite_group_id' => 3, 'gender_id' => 1, 'sort' => 7],
            ['name' => '妹妹', 'group_id' => 4, 'opposite_group_id' => 3, 'gender_id' => 2, 'sort' => 8],
            ['name' => '丈夫', 'group_id' => 5, 'opposite_group_id' => 5, 'gender_id' => 1, 'sort' => 9],
            ['name' => '妻子', 'group_id' => 5, 'opposite_group_id' => 5, 'gender_id' => 2, 'sort' => 10],
            ['name' => '朋友', 'group_id' => 6, 'opposite_group_id' => 6, 'gender_id' => 0, 'sort' => 11],
            ['name' => '师傅', 'group_id' => 7, 'opposite_group_id' => 8, 'gender_id' => 0, 'sort' => 12],
            ['name' => '徒弟', 'group_id' => 8, 'opposite_group_id' => 7, 'gender_id' => 0, 'sort' => 13],
        ];
        DB::table('staff_relative_type')->truncate();
        DB::table('staff_relative_type')->insert($data);
    }

}
