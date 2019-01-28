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
        $list = StaffTmp::whereDate('operate_at', '<=', date('Y-m-d'))->where('status', '<>', 2)->get();
        try {
            foreach ($list as $key => $tmp) {
                $changes = $tmp->changes;
                if (!empty($changes)) {
                    $data = array_merge($changes, [
                        'admin_sn' => $tmp->admin_sn,
                    ]);
                    $result = $this->staffService->update($data);
                    if ($result['status'] === 1) {
                        $tmp->delete();
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error($this->signature.'-|-'.$e->getMessage());
        }
    }

}
