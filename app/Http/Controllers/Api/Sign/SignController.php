<?php

namespace App\Http\Controllers\Api\Sign;

use App\Models\Sign\SignUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Curl;

class SignController extends Controller
{
    //结束时间
    protected $endDateTime = '2018-12-15 18:00:00';

    //答题时间 （秒）
    protected $answerTime = 10;

    //每题基础分值
    protected $init = 30;

    //时间基础分
    protected $timeScore = 2;

    public function __construct()
    {
        if (time() > strtotime($this->endDateTime)) {
            abort(400, '活动已结束');
        }
    }

    /**
     * 获取当前用户
     * @param Request $request
     * @return mixed
     */
    public function getUser(Request $request)
    {
        $code = $request->query('code');
        $result =  app('Dingtalk')->passCodeGetUserInfo($code);
        $data = $this->getUserInfo($result['userid']);
        return response()->json($data,200);
    }

    /**
     * 获取用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getUserInfo($userid)
    {
        $accessToken = app('Dingtalk')->getAccessToken();
        $url = 'https://oapi.dingtalk.com/user/get?access_token='.$accessToken.'&userid='.$userid;
        $result = Curl::setUrl($url)->get();
        return $result;
    }

    /**
     * 签到
     * @param Request $request
     */
    public function sign(Request $request)
    {
        //签到开始时间
        $startDateTime = '2018-12-14 16:00:00';

        $sign = Cache::get('sign');
        if(!is_null($sign)){
            if(time()< strtotime($startDateTime)){
                abort(400,'签到时间还没开始呢，你不能进行签到');
            }
        }

        $this->validate($request, [
            'user_id' => [
                'required',
            ],
            'name' => [
                'string',
                'required'
            ],
            'department' => [
                'string',
                'nullable',
            ],
            'avatar' => [
                'string',
                'nullable'
            ]
        ], [
            'user_id' => "钉钉号",
            'name' => "名字",
            'department' => '部门',
            'avatar' => '头像',
        ]);
        $user = SignUser::where('user_id', $request->input('user_id'))->first();
        if (!is_null($user)) {
            abort(400, '你已签到过了');
        }
        $data = SignUser::create($request->input());
        return response()->json($data, 201);
    }

    /**
     * 点击答题开始
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request)
    {
        $round = $request->query('round');

        $data = [
            //第几回合
            'round' => $round,
            //开始时间
            'dateTime' => time(),
        ];
        switch ($round) {
            case 1:
                $true = 'A';
                break;
            case 2:
                $true = 'B';
                break;
            case 3:
                $true = 'A';
                break;
            case 4:
                $true = 'A';
                break;
            case 5:
                $true = 'A';
                break;
            case 6:
                $true = 'A';
                break;
            case 7:
                $true = 'A';
                break;
            case 8:
                $true = 'A';
                break;
            case 9:
                $true = 'A';
                break;
            case 10:
                $true = 'A';
                break;
            default:
                abort(400, '该活动只有10轮答题');
        }
        $data['true'] = $true;
        Cache::put('start_' . $round, $data, 60 * 24 * 10);
        //设置当前回合开启
        $this->setRound($round);

        return response()->json($data, 200);
    }

    /**
     * 设置当前回合
     * @param $round .
     */
    protected function setRound($round)
    {
        Cache::put('round', $round, 5);
    }

    /**
     * 提交
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submit(Request $request)
    {

        //获取当前回合
        $round = Cache::get('round');
        abort_if(is_null($round), 400, '答题还未开始呢！');
        $this->validate($request, [
            'user_id' => [
                'required'
            ],
            "name" => [
                'required'
            ],
            'avatar' => [
                'string'
            ],
            'true' => [
                'required',
                Rule::in(['A', 'B', 'C', 'D'])
            ]
        ], [], [
            'user_id' => '用户ID',
            'true' => '答案',
            'name' => '名字',
            'avatar' => '头像'
        ]);


        $requestUserId = $request->input('user_id');
        //提交的答案
        $requestTrue = $request->input('true');
        $cache = Cache::get('start_' . $round);
        if (array_has($cache, 'data') && array_has($cache['data'], $requestUserId)) {
            abort('400', '你已经参与过本轮活力了，不能重复提交');
        }

        //答案是否正确
        $isOk = ($cache['true'] == $requestTrue) ? true : false;

        //默认分值
        $score = 0;

        if ($isOk) {
            //获取正确分值
            $score = $this->calculate($cache['dateTime']);
        }

        $timeData = $this->getTime($cache['dateTime']);
        $data = [
            'user_id' => $requestUserId,
            'name' => $request->input('name'),
            'avatar' => $request->input('avatar'),
            'true' => $requestTrue,
            'is_ok' => $isOk,
            'time' => $timeData['time'],
            'str_time' => $timeData['str_time'],
            'score' => $score,
        ];
        $cache['data'][$requestUserId] = $data;
        $cache[$requestTrue][] = $data;
        Cache::put('start_' . $round, $cache, 60 * 24 * 15);


        return response()->json($data, 201);
    }

    /**
     * 计算分值
     */
    protected function calculate($startTime)
    {
        //默认分值
        $score = 0;

        //答题用的时间
        $time = time() - $startTime;

        //答题时间小于等于配置的答题时间
        if ($time <= $this->answerTime) {
            $score = $this->init;
            $score = $score + ($this->timeScore * ($this->answerTime - $time));
        }
        return $score;
    }

    /**
     * 获取答题时间
     * @param $cacheTime
     * @return array
     */
    protected function getTime($cacheTime)
    {
        list($msec, $sec) = explode(' ', microtime());

        $msData = explode('.', $msec);
        $ms = substr($msData[1], 0, 3);
        $time = intval($sec) - $cacheTime;

        $strTime = $time . '秒' . $ms . '毫秒';
        return [
            'str_time' => $strTime,
            'time' => intval($time . $ms),
        ];
    }

    /**
     * 获取当前轮回排名
     * @param Request $request
     */
    public function getTop(Request $request)
    {
        $round = $request->query('round');
        $cache = Cache::get('start_' . $round);
        $a = 0;
        $b = 0;
        $c = 0;
        $d = 0;
        if (array_has($cache, 'A')) {
            $a = count($cache['A']);
        }
        if (array_has($cache, 'B')) {
            $b = count($cache['B']);
        }
        if (array_has($cache, 'C')) {
            $c = count($cache['C']);
        }
        if (array_has($cache, 'D')) {
            $d = count($cache['D']);
        }

        //获取正确前排名10的
        $top = [];
        if (array_has($cache, $cache['true'])) {
            if (count($cache[$cache['true']]) > 10) {
//                $top = [];
                foreach ($cache[$cache['true']] as $k => $v) {
                    if ($k < 10) {
                        $top[] = $v;
                    }
                }
            } else {
                $top = $cache[$cache['true']];
            }
        }

        $data = [
            'round' => $cache['round'],//当前轮回
            'true' => $cache['true'],//正确答案
            'A' => $a,
            'B' => $b,
            'C' => $c,
            'D' => $d,
            'count' => $cache['data'] ? count($cache['data']) : 0,
            'top' => $top
        ];
        return response()->json($data, 200);
    }

    /**
     * 获取全部排行
     */
    public function getAllTop()
    {
        $start1 = Cache::get('start_1');
        $start2 = Cache::get('start_2');
        $start3 = Cache::get('start_3');
        $start4 = Cache::get('start_4');
        $start5 = Cache::get('start_5');
        $start6 = Cache::get('start_6');
        $start7 = Cache::get('start_7');
        $start8 = Cache::get('start_8');
        $start9 = Cache::get('start_9');
        $start10 = Cache::get('start_10');

        $start1True = [];
        if (array_has($start1, $start1['true'])) {
            $start1True = $start1[$start1['true']];
        }
        $start2True = [];
        if (array_has($start2, $start2['true'])) {
            $start2True = $start2[$start2['true']];
        }
        $start3True = [];
        if (array_has($start3, $start3['true'])) {
            $start3True = $start3[$start3['true']];
        }
        $start4True = [];
        if (array_has($start4, $start4['true'])) {
            $start4True = $start4[$start4['true']];
        }
        $start5True = [];
        if (array_has($start5, $start5['true'])) {
            $start5True = $start5[$start5['true']];
        }
        $start6True = [];
        if (array_has($start6, $start6['true'])) {
            $start6True = $start6[$start6['true']];
        }
        $start7True = [];
        if (array_has($start7, $start7['true'])) {
            $start7True = $start7[$start7['true']];
        }
        $start8True = [];
        if (array_has($start8, $start8['true'])) {
            $start8True = $start8[$start8['true']];
        }
        $start9True = [];
        if (array_has($start9, $start9['true'])) {
            $start9True = $start9[$start9['true']];
        }
        $start10True = [];
        if (array_has($start10, $start10['true'])) {
            $start10True = $start10[$start10['true']];
        }

        $data = array_collapse([$start1True, $start2True, $start3True, $start4True, $start5True, $start6True, $start7True, $start8True, $start9True, $start10True]);

        $user = [];
        foreach ($data as $k => $v) {
            if (array_has($user, $v['user_id'])) {
                $user[$v['user_id']]['score'] = $user[$v['user_id']]['score'] + $v['score'];
                $user[$v['user_id']]['total_time'] = $user[$v['user_id']]['total_time'] + $v['time'];
            } else {
                $user[$v['user_id']] = [
                    'user_id' => $v['user_id'],
                    'name' => $v['name'],
                    'avatar' => $v['avatar'],
                    'score' => $v['score'],
                    'total_time' => $v['time']
                ];
            }
        }

        $userCollect = collect($user);
        $topUser = $userCollect->sortByDesc('score')->all();
        return response()->json($topUser, 200);
    }


    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::forget('round');
        Cache::forget('start_1');
        Cache::forget('start_2');
        Cache::forget('start_3');
        Cache::forget('start_4');
        Cache::forget('start_5');
        Cache::forget('start_6');
        Cache::forget('start_7');
        Cache::forget('start_8');
        Cache::forget('start_9');
        Cache::forget('start_10');
        return response('success', 200);
    }

    /**
     * 清除签到数据
     */
    public function clearSign()
    {
        \DB::table('sign_user')->delete();
        return response('success',200);
    }

    /**
     * 开始16点签到
     */
    public function upSign()
    {
        Cache::put('sign',1,60*24*5);
        return response('success',200);
    }

    /**
     * 关闭16点签到
     */
    public function closeSign()
    {
        Cache::forget('sign');
        return response('success',200);
    }
}
