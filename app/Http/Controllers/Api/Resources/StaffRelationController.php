<?php 

namespace App\Http\Controllers\Api\Resources;

use App\Models\HR;
use App\Models\I;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\RelationService;

class StaffRelationController extends Controller
{
    public function index(Request $request)
    {
        $service = new RelationService;
        $newData = [];
        $keys = $service->staffKeys();
        $data = $service->res();
        foreach ($keys as $key => $withKey) {
            if (isset($data[$key])) {
                switch ($key) {
                    case 'account_active':
                        $newData[$withKey] = ($data[$key] == '是') ? 1 : 0;
                        break;
                    case 'shop':
                        $newData[$withKey] = !empty($data[$key]) ? 1 : 0;
                        break;
                    default:
                        $newData[$withKey] = $data[$key];
                        break;
                }
            }
        }
        dd($newData);
        exit;
        $type = in_array($type = $request->query('type', 'status'), [
            'status', 'property', 'national', 'education', 'politics', 'marital', 'relative_type'
        ]) ? $type : 'status';

        return app()->call([$this, camel_case($type)]);
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
     * 获取员工状态列表.
     * 
     * @return array
     */
    public function status()
    {
        return [
            ['id' => 0, 'name' => '离职中'],
            ['id' => 1, 'name' => '试用期'],
            ['id' => 2, 'name' => '在职'],
            ['id' => 3, 'name' => '停薪留职'],
            ['id' => -1, 'name' => '离职'],
            ['id' => -2, 'name' => '自动离职'],
            ['id' => -3, 'name' => '开除'],
            ['id' => -4, 'name' => '劝退'],
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