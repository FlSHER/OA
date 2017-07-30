<?php

namespace App\Http\Controllers\HR;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\HR\Shop;
use App\Contracts\CURD;

class ShopController extends Controller {

    protected $model = 'App\Models\HR\Shop';

    public function __construct(CURD $curd) {
        $this->curdService = $curd->model($this->model);
    }

    public function showManagePage() {
        return view('hr.shop');
    }

    public function getList(Request $request) {
        return app('Plugin')->dataTables($request, Shop::visible());
    }

    public function getInfo(Request $request) {
        $id = $request->id;
        $shop = Shop::with(['staff'])->find($id);
        return $shop;
    }

    public function addOrEdit(Request $request) {
        $this->validate($request, $this->makeValidator($request), [],trans('fields.shop'));
        return $this->curdService->createOrUpdate($request->all());
    }

    /**
     * 删除
     * @param Request $request
     * @return array
     */
    public function deleteByOne(Request $request) {
        $response = $this->curdService->delete($request->id, ['staff']);
        return $response;
    }

    /**
     * 搜索店铺插件
     * @param Request $request
     * @return type
     */
    public function showSearchResult(Request $request) {
        $data['name'] = $request->name;
        $data['target'] = json_encode($request->target);
        $data['mark'] = $request->has('mark') ? '_' . $request->mark : '';
        return view('hr/search_shop')->with($data);
    }

    protected function makeValidator(Request $request) {
        return [
            'shop_sn' => ['required', 'max:10', 'unique:shops,shop_sn,' . $request->id . ',id,deleted_at,NULL'],
            'name' => ['required', 'max:50'],
            'department_id' => ['required', 'integer', 'min:2', 'exists:departments,id,deleted_at,NULL'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'province_id' => ['required', 'exists:i_district,id,level,1'],
            'city_id' => ['required', 'exists:i_district,id,level,2'],
            'county_id' => ['required', 'exists:i_district,id,level,3'],
            'address' => ['required', 'max:50'],
            'manager_sn' => ['required_with:manager_name'],
            'manager_name' => [],
        ];
    }

}
