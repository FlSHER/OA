<?php

use Illuminate\Database\Seeder;

class StaffTransferTagSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            ['name'=>'休假结束','sort'=>'1'],
            ['name'=>'新店开业','sort'=>'2'],
        ];
        DB::table('staff_transfer_tags')->insert($data);
    }

}
