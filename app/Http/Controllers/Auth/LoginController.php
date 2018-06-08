<?php

/**
 * 后台登录控制器
 * create by Fisher 2016/8/26 <fisher9389@sina.com>
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\HR\Staff;
use Encypt;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        login as protected loginByUsername;
    }

    protected $username;
    protected $password;
    protected $admin;
    protected $response;

    protected $redirectTo = '/';

    /**
     * 显示后台登录界面
     * @return html
     */
    public function showLoginForm()
    {
        session()->reflash();
        $app = app('Menu')->getAppData();
        return response()->view('login', ['app' => $app]);
    }

    public function username()
    {
        return 'mobile';
    }

    public function login(Request $request)
    {
        if ($request->has('dingtalk_auth_code')) {
            return $this->loginByDingtalkAuthCode($request);
        } else {
            $response = $this->loginByUsername($request);
            if ($response instanceof RedirectResponse) {
                $url = $response->getTargetUrl();
                if (request()->isXmlHttpRequest()) {
                    return ['status' => 1, 'url' => $url];
                } else {
                    return redirect($url)->withInput()->withErrors('为了保证您的账户安全！请立即修改初始密码');
                }
            } else {
                return $response;
            }
        }
    }

    /**
     * 登录表单验证
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'dingding' => 'string|max:50',
        ];
        $this->validate($request, $rules);
    }


    /**
     * 登录失败，返回失败原因
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        session()->keep(['url']);
        if (request()->isXmlHttpRequest()) {
            return ['status' => -1, 'message' => trans('auth.failed')];
        } else {
            return redirect()->back()->withInput()->withErrors(['login_fail' => $this->response]);
        }
    }

    /**
     * 显示密码重置界面
     */
    public function showResetPage()
    {
        session()->reflash();
        return view('reset_password');
    }

    /**
     * 重置密码
     */
    public function resetPassword(Request $request)
    {
        $this->validateResetPassword($request);
        $this->password = $request->old_pwd;
        $staffSn = app('CurrentUser')->getStaffSn();
        $this->admin = Staff::find($staffSn);
        if ($this->checkPassword()) {
            $password = $request->password; //新密码
            $newSalt = mt_rand(100000, 999999);
            $this->encyptPassword($password, $newSalt);
            $this->admin->password = $this->password;
            $this->admin->salt = $newSalt;
            $this->admin->save();
        } else {
            return ['status' => -1, 'message' => $this->response];
        }
        $url = session()->has('url') ? session()->get('url') : $request->has('redirect_uri') ? $request->redirect_uri : '/';
        return ['status' => 1, 'url' => $url];
    }

    /**
     * 验证修改密码
     * @param type $request
     */
    private function validateResetPassword($request)
    {
        $this->validate($request, [
            'old_pwd' => 'required',
            'password' => 'required|different:old_pwd|confirmed',
            'password_confirmation' => 'required',
        ], [], ['old_pwd' => '原密码', 'password' => '新密码', 'password_confirmation' => '确认新密码']
        );
    }

    /**
     * 检查用户名是否存在
     * @return boolean
     */
    private function checkUsername()
    {
        $admin = $this->getAdminByUsername($this->username);
        if (empty($admin)) {
            $this->response = '用户名不存在，请联系人事核对手机号码';
            return false;
        } else {
            $this->admin = $admin;
            return true;
        }
    }

    /**
     * 通过用户名获取Admin
     * @return object
     */
    private function getAdminByUsername($username)
    {
        return Staff::where(function ($query) use ($username) {
            $query->orWhere(['username' => $username])
                ->orWhere(['mobile' => $username]);
        })->where([['is_active', '=', 1], ['status_id', '>=', 0]])->first();
    }

    /**
     * 检查密码是否正确
     */
    private function checkPassword()
    {
        $this->encyptPassword($this->password, $this->admin['salt']);
        if ($this->admin['password'] == $this->password) {
            return true;
        } else {
            $this->response = '密码错误！';
            return false;
        }
    }

    /**
     * 通过设置的方法加密密码
     */
    private function encyptPassword($password, $salt)
    {
        $this->password = Encypt::password($password, $salt);
    }

    /**
     * 登录成功
     * @return view
     */
    private function loginSuccess()
    {
        if (request()->has('dingding') && !empty(request()->input('dingding'))) {
            $this->admin->dingding = request()->input('dingding');
        }
        $this->putAdminInfoInSession($this->admin);
        $this->updateLoginInfo();
        if (empty(request()->url)) {
            $url = route('home');
        } else {
            $url = request()->url;
            $url = str_replace('*', '/', $url);
        }
        $url = $this->updateDefaultPassword($url); //默认密码为123456的进行修改
        if (request()->isXmlHttpRequest()) {
            return ['status' => 1, 'url' => $url];
        } else {
            return redirect($url)->withInput()->withErrors('为了保证您的账户安全！请立即修改初始密码');
        }
    }

    /**
     * 将用户信息存入SESSION
     */
    private function putAdminInfoInSession($admin)
    {
        $data = array_except($admin, ['password', 'salt']);
        session()->put('admin', $data);
    }

    /**
     * 更新最近登录信息
     */
    private function updateLoginInfo()
    {
        if ($this->admin['staff_sn'] == config('auth.developer.staff_sn')) {
            return false;
        }
        $this->admin->latest_login_time = time();
        $this->admin->latest_login_ip = request()->getClientIp();
        $this->admin->save();
    }


    /**
     * 使用开发者账户登录
     */
    private function loginAsDeveloper()
    {
        $developer = config('auth.developer');
        if ($this->username == $developer['username'] && $this->password == $developer['password']) {
            $this->admin = $developer;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检测钉钉登录
     */
    public function loginByDingtalkAuthCode(Request $request)
    {
        $code = $request->dingtalk_auth_code;
        $userInfo = app('Dingtalk')->passCodeGetUserInfo($code); //通过CODE换取用户身份
        if (empty($userInfo['userid'])) {
            return ['status' => -1, 'message' => '钉钉免登失败，请手动登录'];
        }
        $dingtalkId = $userInfo['userid'];
        $dingDingUser = Staff::where('dingding', $dingtalkId)->first();
        if ($dingDingUser) {
            $this->username = $dingDingUser->mobile;
            if ($this->checkUsername()) {
                return $this->loginSuccess();
            }
            return $this->sendFailedLoginResponse();
        } else {
            return ['status' => -2, 'message' => '钉钉账号未同步，请手动登录', 'dingding' => $dingtalkId];
        }
    }

    /**
     * 登录成功修改默认密码（123456）
     */
    private function updateDefaultPassword($url)
    {
        session()->keep(['url']);
        if (request()->get('password') === '123456') {
            $url = route('reset');
        }
        return $url;
    }

}
