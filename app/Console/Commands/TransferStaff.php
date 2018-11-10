<?php

namespace App\Console\Commands;

use App\Models\HR\Staff;
use App\Models\HR\StaffTmp;
use App\Services\StaffService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TransferStaff extends Command
{
    /**
     * staff service.
     * 
     * @var object
     */
    protected $staffService;

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
    public function __construct(StaffService $service)
    {
        parent::__construct();

        $this->staffService = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $list = StaffTmp::whereDate('operate_at', '<=', date('Y-m-d'))->get();
        try {

            \DB::beginTransaction();
            foreach ($list as $key => $tmp) {
                $staff = Staff::find($tmp->staff_sn);
                $changes = array_filter($tmp->changes);
                if (!empty($changes) && !empty($staff)) {

                    $data = array_merge($changes, [
                        'staff_sn' => $staff->staff_sn,
                        'admin_sn' => $tmp->admin_sn,
                        'operate_at' => date('Y-m-d'),
                        'operation_type' => 'transfer',
                        'operation_remark' => '执行预约任务',
                    ]);
                    $result = $this->staffService->update($data);
                    if ($result['status'] === 1) {
                        $tmp->delete();
                    }
                }
            }
            \DB::commit();

        } catch (\Exception $e) {

            \DB::rollBack();
            Log::error($this->signature.'-|-'.$e->getMessage());
        }
    }

}
