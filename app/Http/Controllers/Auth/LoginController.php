<?php

/**
 * 后台登录控制器
 * create by Fisher 2016/8/26 <fisher9389@sina.com>
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\HR\Staff;
use Encypt;

class LoginController extends Controller
{
    use AuthenticatesUsers;

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

    /**
     * 登录表单验证
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => ['required_without:dingtalk_auth_code', 'string'],
            'password' => ['required_without:dingtalk_auth_code', 'string'],
            'dingtalk_auth_code' => 'string|max:50',
        ], ['mobile.required_without' => '用户名不能为空', 'password.required_without' => '密码不能为空']);
    }

    protected function authenticated(Request $request, $user)
    {
        $url = redirect()->intended($this->redirectPath())->getTargetUrl();
        if ($request->has('password') && $request->password == 123456) {
            return ['status' => 1, 'url' => '/reset_password?redirect_uri=' . $url];
        } else {
            return ['status' => 1, 'url' => $url];
        }
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password', 'dingtalk_auth_code');
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
        $url = $request->has('redirect_uri') ? $request->redirect_uri : '/';
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

}
