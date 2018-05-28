<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Services\HRMService;
use App\Services\PluginService;
use App\Models\Brand;

class PositionController extends Controller {

    public function showManagePage() {
        $data['brand'] = Brand::orderBy('sort', 'asc')->get();
        return view('hr.position')->with($data);
    }

    /**
     * dateTables获取职位信息
     * @param Request $request
     * @param PluginService $plugin
     * @return type
     */
    public function getPositionList(Request $request, PluginService $plugin) {
        return $plugin->dataTables($request, new Position);
    }

    /**
     * 添加单个职位
     * @param Request $request
     * @return type
     */
    public function addPositionByOne(Request $request) {
        $data = $request->except(['_url', '_token', 'brand']);
        $brand = $request->has('brand') ? $request->brand : [];
        Position::create($data)->brand()->sync($brand);
        return ['status' => 1, 'message' => '添加成功'];
    }

    /**
     * 编辑单个职位
     * @param Request $request
     * @return type
     */
    public function editPositionByOne(Request $request) {
        $data = $request->except(['_url', '_token', 'brand']);
        $id = $request->id;
        $brand = $request->has('brand') ? $request->brand : [];
        $position = Position::find($id);
        $position->update($data);
        $position->brand()->sync($brand);
        return ['status' => 1, 'message' => '编辑成功'];
    }

    /**
     * 删除职位
     * @param Request $request
     * @return type
     */
    public function deletePosition(Request $request) {
        $id = $request->id;
        Position::find($id)->delete();
        return ['status' => 1, 'message' => '删除成功'];
    }

    /**
     * 获取职位下拉选项
     * @param Request $request
     * @param \App\Services\HRMService $HRM
     * @return type
     */
    public function getOptionsById(Request $request, HRMService $HRM) {
        return $HRM->getPositionOptionsByBrandId($request->brand_id);
    }

}
