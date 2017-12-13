<?php

namespace App\Console\Commands;

use App\Models\HR\Attendance\Attendance;
use Illuminate\Console\Command;
use DB;

class GetSalePerformanceFromTDOA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:getSalePerformance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Sale Performance From TD_OA';

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
        $startDate = date('Y-m-d', strtotime('-20 days'));
        $data = DB::connection('TDOA')->table('flow_data_42 AS a')
            ->join('flow_run AS b', 'a.run_id', '=', 'b.run_id')
            ->select(
                'a.data_7 as shop_sn',
                'a.data_8 as attendance_date',
                DB::raw('SUM(a.data_62+a.data_25+a.data_26+a.data_27) as sale_performance'),
                DB::raw('!ISNULL(MIN(b.END_TIME)) as is_end')
            )
            ->groupBy('a.data_7', 'a.data_8')
            ->where('b.DEL_FLAG', '=', 0)
            ->where(DB::raw('STR_TO_DATE(a.data_8,"%Y-%m-%d")'), '>', $startDate)
            ->get()->toJson();
        DB::connection('attendance')->table('tdoa_sale_performance')->delete();
        DB::connection('attendance')->table('tdoa_sale_performance')->insert(json_decode($data, true));
        $updateSql = 'UPDATE attendance_shop a 
LEFT JOIN tdoa_sale_performance b ON a.shop_sn = b.shop_sn AND a.attendance_date = b.attendance_date 
SET a.tdoa_sales_performance = IFNULL(b.sale_performance,0) WHERE a.attendance_date > "' . $startDate . '"';
        DB::connection('attendance')->update($updateSql);
    }
}
