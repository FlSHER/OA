<?php 

namespace App\Http\Controllers\Api\Resources;

use App\Models\HR;
use App\Models\I;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class StaffRelationController extends Controller
{
    /**
     * 获取员工状态列表.
     * 
     * @return array
     */
    public function status()
    {
        $list = HR\StaffStatus::query()
            ->filterByQueryString()
            ->sortByQueryString()
            ->get();

        return response()->json($list, 200);
    }

    /**
     * 获取员工属性.
     * 
     * @return array
     */
    public function property()
    {
        return [
            ['id' => 0, 'name' => '无'],
            ['id' => 1, 'name' => '109将'],
            ['id' => 2, 'name' => '36天罡'],
            ['id' => 3, 'name' => '24金刚'],
            ['id' => 4, 'name' => '18罗汉'],
        ];
    }

    /**
     * 获取全部民族.
     * 
     * @return array
     */
    public function national()
    {
        return response()->json($this->hasCache('staff_relation_national'), 200);
    }

    /**
     * 获取全部学历.
     * 
     * @return array
     */
    public function education()
    {
        return response()->json($this->hasCache('staff_relation_education'), 200);
    }

    /**
     * 获取政治面貌。
     * 
     * @return array
     */
    public function politics()
    {
        return response()->json($this->hasCache('staff_relation_politics'), 200);
    }

    /**
     * 获取婚姻状态选项.
     * 
     * @return array
     */
    public function marital()
    {
        return response()->json($this->hasCache('staff_relation_marital'), 200);
    }

    /**
     * 获取关系类型选项.
     * 
     * @return array
     */
    public function relativeType()
    {
        return response()->json($this->hasCache('staff_relation_relative_type'), 200);
    }

    /**
     * 缓存不常修改的数据.
     * 
     * @param  array  $data
     * @param  string  $key
     * @return array
     */
    protected function hasCache($cacheKey)
    {
        if (Cache::has($cacheKey)) {

            return Cache::get($cacheKey);
        }
        switch ($cacheKey) {
            case 'staff_relation_national':
                $data = I\National::get();
                break;
            case 'staff_relation_education':
                $data = I\Education::get();
                break;
            case 'staff_relation_politics':
                $data = I\Politics::get();
                break;
            case 'staff_relation_marital':
                $data = I\MaritalStatus::get();
                break;
            case 'staff_relation_relative_type':
                $data = HR\StaffRelativeType::get();
                break;
        }
        Cache::put($cacheKey, $data, now()->addDay());

        return $data;
    }
}