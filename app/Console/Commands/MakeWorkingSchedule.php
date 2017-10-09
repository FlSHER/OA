<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class MakeWorkingSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:makeSchedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create working schedule with HR data.';

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
        Log::info('Start making working schedule');
        $tableYesterday = DB::connection('attendance')->select('SELECT * FROM information_schema.TABLES WHERE table_name ="working_schedule_' . date('Ymd', strtotime("-1 day")) . '"');
        $tableToday = DB::connection('attendance')->select('SELECT * FROM information_schema.TABLES WHERE table_name ="working_schedule_' . date('Ymd') . '"');
        if (empty($tableToday) && !empty($tableYesterday)) {
            DB::connection('attendance')->statement('CREATE TABLE working_schedule_' . date('Ymd') . ' LIKE working_schedule_' . date('Ymd', strtotime("-1 day")));
            DB::connection('attendance')->statement('INSERT INTO working_schedule_' . date('Ymd') . ' SELECT * FROM working_schedule_' . date('Ymd', strtotime("-1 day")));
        } elseif (empty($tableToday)) {
            Log::error('Table working_schedule dosen\'t exists');
            $this->info('Table working_schedule dosen\'t exists');
            return false;
        }
        $addCount = 0;
        $deleteCount = 0;

        $basicList = DB::table('staff')
            ->select(DB::raw('CONCAT(`shop_sn`,"-",`staff_sn`) AS sn'), 'staff_sn', 'shop_sn', 'realname')
            ->where([
                ['shop_sn', '<>', ''],
                ['status_id', '>=', '0']
            ])->whereNull('deleted_at')->get();
        $basicSnList = $basicList->pluck('sn')->all();
        $basicStaffList = $basicList->pluck('realname', 'staff_sn')->all();

        $scheduleSnList = DB::connection('attendance')
            ->table('working_schedule_' . date('Ymd'))
            ->select(DB::raw('CONCAT(`shop_sn`,"-",`staff_sn`) AS sn'))
            ->pluck('sn')->all();

        $addList = empty($scheduleSnList) ? $basicSnList : array_diff($basicSnList, $scheduleSnList);
        $deleteList = empty($scheduleSnList) ? [] : array_diff($scheduleSnList, $basicSnList);

        foreach ($addList as $sn) {
            $addCount++;
            $staffSn = substr($sn, -6);
            $shopSn = substr($sn, 0, -7);
            DB::connection('attendance')
                ->table('working_schedule_' . date('Ymd'))
                ->insert(['shop_sn' => $shopSn, 'staff_sn' => $staffSn, 'staff_name' => $basicStaffList[$staffSn]]);
        }

        foreach ($deleteList as $sn) {
            $deleteCount++;
            DB::connection('attendance')
                ->table('working_schedule_' . date('Ymd'))
                ->where(['shop_sn' => $shopSn, 'staff_sn' => $staffSn])
                ->delete();
        }

        Log::info('add:' . $addCount . ' delete:' . $deleteCount);
        DB::connection('attendance')->select('DROP TABLE IF EXISTS working_schedule_' . date('Ymd', strtotime("-7 day")));
        Log::info('End making working schedule');
    }
}
