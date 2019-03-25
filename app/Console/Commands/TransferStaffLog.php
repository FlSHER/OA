<?php

namespace App\Console\Commands;

use App\Models\HR;
use Illuminate\Console\Command;

class TransferStaffLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:stafflog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '转换员工日志';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        HR\StaffLog::query()
        ->where('is_show', 0)
        ->whereIn('operation_type', ['transfer', 'import_transfer', 'position'])
        ->select(['is_show', 'operation_type', 'changes', 'id'])
        ->chunk(5000, function ($logs) {
            $bar = $this->output->createProgressBar(count($logs));
            foreach ($logs as $key => $log) {
                try {
                    $type = $log->operation_type;
                    if (in_array($type, ['人事变动', '导入变动', '职位变动']) && !empty($log->changes)) {
                        $allow = ['department', 'position', 'brand', 'status', 'cost_brands', '部门全称', '职位', '品牌', '员工状态', '费用品牌'];
                        collect($log->changes)->filter(function ($v, $k) use ($log, $allow) {
                            if (in_array($k, $allow)) {
                                $log->is_show = 1;
                            }
                        });
                    } elseif (in_array($type, ['入职', '转正', '离职', '再入职'])) {
                        $log->is_show = 1;
                    }
                    $log->save();
                    $bar->advance();
                } catch (\Exception $e) {
                    \Log::error($this->signature.'-|-'.$e->getMessage());
                }
            }
            $bar->finish();
        });
    }
}
