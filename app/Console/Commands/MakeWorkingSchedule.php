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

    protected $holdDays = 40;

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
        $this->copySchedule();

        $addCount = 0;
        $deleteCount = 0;
        $managerCount = 0;

        $basicList = $this->getBasicList();
        $basicSnList = $basicList->pluck('sn')->all();
        $basicStaffList = $basicList->pluck('realname', 'staff_sn')->all();

        $scheduleList = $this->getScheduleList();
        $scheduleSnList = $scheduleList->pluck('sn')->all();

        $addList = array_diff($basicSnList, $scheduleSnList);
        $deleteList = array_diff($scheduleSnList, $basicSnList);

        $this->add($addList, $addCount, $basicStaffList);
        $this->delete($deleteList, $deleteCount);

        $shopManagerList = $this->getShopManagerList();
        $shopManagerList->each(function ($shopManager) use (&$managerCount) {
            $manager = DB::connection('attendance')
                ->table('working_schedule_' . date('Ymd'))
                ->where('shop_sn', $shopManager->shop_sn)
                ->where('staff_sn', $shopManager->manager_sn)
                ->first();
            if ($manager && $manager->shop_duty_id != 1) {
                DB::connection('attendance')
                    ->table('working_schedule_' . date('Ymd'))
                    ->where('shop_sn', $shopManager->shop_sn)
                    ->where('shop_duty_id', 1)
                    ->update(['shop_duty_id' => 3]);
                DB::connection('attendance')
                    ->table('working_schedule_' . date('Ymd'))
                    ->where('shop_sn', $shopManager->shop_sn)
                    ->where('staff_sn', $shopManager->manager_sn)
                    ->update(['shop_duty_id' => 1]);
                $managerCount++;
            }
        });

        $this->saveClockInAndClockOutTime();

        DB::connection('attendance')->select('DROP TABLE IF EXISTS working_schedule_' . date('Ymd', strtotime('-' . $this->holdDays . ' day')));
    }

    protected function copySchedule()
    {
        $tableYesterday = DB::connection('attendance')
            ->table('information_schema.TABLES')
            ->where('table_name', 'working_schedule_' . date('Ymd', strtotime("-1 day")))
            ->count();
        $tableToday = DB::connection('attendance')
            ->table('information_schema.TABLES')
            ->where('table_name', 'working_schedule_' . date('Ymd'))
            ->count();

        if (!$tableToday && $tableYesterday) {
            DB::connection('attendance')->statement('CREATE TABLE working_schedule_' . date('Ymd') . ' LIKE working_schedule_' . date('Ymd', strtotime("-1 day")));
            DB::connection('attendance')->statement('INSERT INTO working_schedule_' . date('Ymd') . ' SELECT * FROM working_schedule_' . date('Ymd', strtotime("-1 day")));
        } elseif (!$tableToday) {
            Log::error('Table working_schedule dosen\'t exists');
            $this->info('Table working_schedule dosen\'t exists');
            die;
        }
    }

    protected function getBasicList()
    {
        $basicList = DB::table('staff')
            ->select(DB::raw('CONCAT(`shop_sn`,"-",`staff_sn`) AS sn'), 'staff_sn', 'shop_sn', 'realname')
            ->where([
                ['shop_sn', '<>', ''],
                ['status_id', '>=', '0']
            ])->whereNull('deleted_at')->get();
        return $basicList;
    }

    protected function getScheduleList()
    {
        $scheduleList = DB::connection('attendance')
            ->table('working_schedule_' . date('Ymd'))
            ->select(DB::raw('CONCAT(`shop_sn`,"-",`staff_sn`) AS sn'))
            ->get();
        return $scheduleList;
    }


    protected function getShopManagerList()
    {
        $shopManagerList = DB::table('shops')
            ->where('manager_sn', '>', 0)
            ->get();
        return $shopManagerList;
    }

    protected function add($addList, &$addCount, $basicStaffList)
    {
        foreach ($addList as $sn) {
            try {
                $staffSn = substr($sn, -6);
                $shopSn = substr($sn, 0, -7);
                DB::connection('attendance')
                    ->table('working_schedule_' . date('Ymd'))
                    ->insert(['shop_sn' => $shopSn, 'staff_sn' => $staffSn, 'staff_name' => $basicStaffList[$staffSn]]);
                $addCount++;
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    protected function delete($deleteList, &$deleteCount)
    {
        foreach ($deleteList as $sn) {
            try {
                $staffSn = substr($sn, -6);
                $shopSn = substr($sn, 0, -7);
                DB::connection('attendance')
                    ->table('working_schedule_' . date('Ymd'))
                    ->where(['shop_sn' => $shopSn, 'staff_sn' => $staffSn])
                    ->delete();
                $deleteCount++;
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

    protected function saveClockInAndClockOutTime()
    {
        DB::table('shops')->get()->each(function ($model) {
            DB::connection('attendance')
                ->table('working_schedule_' . date('Ymd', strtotime("-1 day")))
                ->where('shop_sn', $model->shop_sn)
                ->whereNull('clock_in')
                ->update(['clock_in' => $model->clock_in]);
            DB::connection('attendance')
                ->table('working_schedule_' . date('Ymd', strtotime("-1 day")))
                ->where('shop_sn', $model->shop_sn)
                ->whereNull('clock_out')
                ->update(['clock_out' => $model->clock_out]);
        });
    }

}
