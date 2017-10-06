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
        DB::table('staff')
            ->select(DB::raw('GROUP_CONCAT(staff_sn) AS staff_sn'), 'shop_sn')
            ->where('shop_sn', '<>', '')->groupBy('shop_sn')
            ->get()->each(function ($model) use ($addCount, $deleteCount) {
                $BasicStaffSnList = explode(',', $model->staff_sn);
                $ScheduleStaffSnList = DB::connection('attendance')
                    ->table('working_schedule_' . date('Ymd'))
                    ->where('shop_sn', $model->shop_sn)->get()->pluck('staff_sn');
                $ScheduleStaffSnList = json_decode($ScheduleStaffSnList);
                $addList = empty($ScheduleStaffSnList) ? $BasicStaffSnList : array_diff($BasicStaffSnList, $ScheduleStaffSnList);
                $deleteList = empty($ScheduleStaffSnList) ? [] : array_diff($ScheduleStaffSnList, $BasicStaffSnList);
                foreach ($addList as $staffSn) {
                    $addCount++;
                    DB::connection('attendance')
                        ->table('working_schedule_' . date('Ymd'))
                        ->insert(['shop_sn' => $model->shop_sn, 'staff_sn' => $staffSn]);
                }
                foreach ($deleteList as $staffSn) {
                    $deleteCount++;
                    DB::connection('attendance')
                        ->table('working_schedule_' . date('Ymd'))
                        ->where(['shop_sn' => $model->shop_sn, 'staff_sn' => $staffSn])
                        ->delete();
                }
            });
        Log::info('add:' . $addCount . ' delete:' . $deleteCount);
        DB::connection('attendance')->select('DROP TABLE IF EXISTS working_schedule_' . date('Ymd', strtotime("-7 day")));
        Log::info('End making working schedule');
    }
}
