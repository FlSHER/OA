<?php

declare(strict_types=1);

namespace Fisher\Amap\API\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Fisher\Amap\Models\AroundAmap;

class HomeController
{
    // 高德自定义地图创建数据接口
    protected $createUri = 'http://yuntuapi.amap.com/datamanage/data/create';

    // 高德自定义地图更新数据接口
    protected $updateUri = 'http://yuntuapi.amap.com/datamanage/data/update';

    // 高德自定义地图删除数据接口
    protected $deleteUri = 'http://yuntuapi.amap.com/datamanage/data/delete';

    // 高德自定义地图查询数据接口
    protected $searchUri = 'http://yuntuapi.amap.com/datasearch/around?';

    // 高德地图地理位置逆编码接口
    protected $getgeoUri = 'http://restapi.amap.com/v3/geocode/geo?';

    protected $client;

    /**
     * 高德应用的KEY
     *
     * @var string
     */
    protected $amap_key;

    /**
     * 高德应用的密钥
     *
     * @var string
     */
    protected $amap_sig;

    /**
     * 高德自定义地图ID
     *
     * @var string
     */
    protected $amap_tableId;

    protected $errors = [
        'INVALID_USER_KEY' => 'KEY无效',
        'SERVICE_NOT_EXIST' => '请求的服务不存在',
        'USER_VISIT_TOO_FREQUENTLY' => '请求过于频繁',
        'INVALID_USER_SIGNATURE' => '数字签名错误',
        'INVALID_USER_SCode' => '用户安全码未通过',
        'SERVICE_NOT_AVAILABLE' => '没有使用该接口的权限',
        'DAILY_QUERY_OVER_LIMIT' => '访问已超出日访问量',
        'ACCESS_TOO_FREQUENT' => '单位时间内访问过于频繁',
        'USERKEY_PLAT_NOMATCH' => '请求key与绑定平台不符',
        'IP_QUERY_OVER_LIMIT' => 'IP访问超限',
        'INSUFFICIENT_PRIVILEGES' => '权限不足，服务请求被拒绝',
        'QPS_HAS_EXCEEDED_THE_LIMIT' => '云图服务QPS超限',
        'INVALID_PARAMS' => '请求参数非法',
        'MISSING_REQUIRED_PARAMS' => '缺少必填参数',
        'UNKNOWN_ERROR' => '未知错误',
        'ENGINE_RESPONSE_DATA_ERROR' => '服务响应失败',
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->amap_key = config('amap.amap_key');
        $this->amap_sig = config('amap.amap_sig');
        $this->amap_tableId = config('amap.amap_tableId');
    }

    /**
     * 数据总线.
     *
     * @author 28youth
     * @param Request $request
     * @param AroundAmap $around
     * @return void
     */
    public function index(Request $request, AroundAmap $around)
    {
        $aroundAmap = $around->find($request->shop_sn);
        if ($aroundAmap) {
            return $this->update($request, $aroundAmap);
        } else {
            return $this->create($request, $around);
        }
    }

    /**
     * 创建高德地图中的自定义位置
     *
     * @author 28youth
     * @param Request $request
     * @param AroundAmap $around
     * @return void
     */
    public function create(Request $request, AroundAmap $around)
    {
        $shopsn = $request->input('shop_sn');
        $shopname = $request->input('shop_name');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        if (! $latitude) {
            return response()->json(['message' => '请传递GPS纬度坐标'], 400);
        }
        if (! $longitude) {
            return response()->json(['message' => '请传递GPS经度坐标'], 400);
        }
        $around->shop_sn = $shopsn;
        $around->longitude = $longitude;
        $around->latitude = $latitude;
        $_location = $longitude.','.$latitude;
        $data = json_encode([
            '_name' => $shopname,
            '_location' => $_location,
            'shop_sn' => $shopsn,
        ]);
        $prams = [
            'data' => $data,
            'key' => $this->amap_key,
            'tableid' => $this->amap_tableId,
        ];
        $prams['sig'] = md5(urldecode(http_build_query($prams, '', '&')).$this->amap_sig);
        $result = json_decode($this->client->post($this->createUri, [
            'form_params' => $prams,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ])
            ->getBody()
            ->getContents(), true);
                
        if ($result['status'] === 1) {
            $around->_id = $result['_id'];
            $around->save();

            return response()->json(['message' => '位置创建成功', 'status' => 1], 201);
        } else {
            return response()->json(['message' => $this->errors[$result['info']] ?? '未知错误'], 500);
        }
    }

    /**
     * 更新高德自定义地图中的位置
     *
     * @author 28youth
     * @param Request $request
     * @param AroundAmap $around
     * @return void
     */
    public function update(Request $request, AroundAmap $around)
    {
        $shopsn = $request->input('shop_sn');
        $shopname = $request->input('shop_name');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $aroundAmap = $around->find($shopsn);
        if (! $aroundAmap) {
            return response()->json(['message' => '系统错误, 请联系管理员'], 500);
        }
        if (! $latitude) {
            return response()->json(['message' => '纬度坐标获取失败'], 400);
        }
        if (! $longitude) {
            return response()->json(['message' => '经度坐标获取失败'], 400);
        }
        $_location = $longitude.','.$latitude;
        $data = json_encode([
            '_id' => $aroundAmap->_id,
            '_name' => $shopname,
            '_location' => $_location,
            'shop_sn' => $shopsn
        ]);
        $prams = [
            'data' => $data,
            'key' => $this->amap_key,
            'tableid' => $this->amap_tableId,
        ];
        $prams['sig'] = md5(urldecode(http_build_query($prams, '', '&')).$this->amap_sig);
        $result = json_decode($this->client->post($this->updateUri, [
            'form_params' => $prams,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ])
            ->getBody()
            ->getContents(), true);
        if ($result['status'] === 1) {
            $aroundAmap->longitude = $longitude;
            $aroundAmap->latitude = $latitude;
            $aroundAmap->save();

            return response()->json(['message' => '位置更新成功', 'status' => 1], 201);
        } else {
            return response()->json(['message' => $this->errors[$result['info']] ?? '未知错误'], 500);
        }
    }

    /**
     * 清除高德自定义地图中的位置.
     *
     * @author 28youth
     * @param Request $request
     * @param AroundAmap $around
     * @return void
     */
    public function delete(Request $request, AroundAmap $around)
    {
        $aroundAmap = $around->find($request->shop_sn);
        $parmas = [
            'ids' => $aroundAmap->_id,
            'key' => $this->amap_key,
            'tableid' => $this->amap_tableId,
        ];
        $parmas['sig'] = md5(urldecode(http_build_query($parmas, '', '&')).$this->amap_sig);
        $result = json_decode($this->client->post($this->deleteUri, [
            'form_params' => $parmas,
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ])
            ->getBody()
            ->getContents(), true);

        if ($result['status'] && ! $result['fail']) {
            $aroundAmap->delete();

            return response()->json(null, 204);
        } else {
            return response()->json(['message' => $this->errors[$result['info']] ?? '未知错误'], 500);
        }
    }

    /**
     * 获取某个位置范围内的店铺
     *
     * @param Request $request
     * @param AroundAmap $around
     * @return void
     */
    public function getArounds(Request $request, AroundAmap $around)
    {
        // todo
    }

}
