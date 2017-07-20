<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HR\Staff;
use DB;

class TransferStaff extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer & employ the staff in table:staff_tmp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $list = DB::table('staff_tmp')->where('operate_at', '<=', date('Y-m-d'));
        $list->get()->map(function($staff) {
            $staffSn = $staff->staff_sn;
            $data = array_filter(array_except((array)$staff, 'operate_at'));
            Staff::find($staffSn)->update($data);
        });
        $list->delete();
    }

}
