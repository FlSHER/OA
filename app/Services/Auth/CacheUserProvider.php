<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/1/6 0006
 * Time: 14:48
 */

namespace App\Services\Auth;


use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Encypt;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CacheUserProvider extends EloquentUserProvider
{

    protected $minutes = 120;

    public function retrieveById($identifier)
    {
        $developer = config('auth.developer');
        if ($identifier == $developer['staff_sn']) {
            $attribute = $developer;
        } else {
            $attribute = Cache::remember('staff_' . $identifier, $this->minutes, function () use ($identifier) {
                $user = parent::retrieveById($identifier);
                return $user instanceof Model ? $user->toArray() : $user;
            });
        }
        return new AuthenticatableUser($attribute);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $developer = config('auth.developer');
        $query = $this->createModel()->newQuery()
            ->where('is_active', 1)
            ->where('status_id', '>=', 0);
        if (array_has($credentials, 'dingtalk_auth_code')) {
            $code = $credentials['dingtalk_auth_code'];
            $userInfo = app('Dingtalk')->passCodeGetUserInfo($code);
            if (empty($userInfo['userid'])) {
                abort(400, '钉钉免登失败，请手动登录');
            }
            $dingtalkId = $userInfo['userid'];
            $user = $query->where('dingtalk_number', $dingtalkId)->first();
            if ($user) {
                return $user;
            } else {
                abort(400, '钉钉账号未同步，请手动登录', ['dingtalk_number' => $dingtalkId]);
            }
        } elseif ($credentials['mobile'] == $developer['username']) {
            $developer['password'] = Encypt::password($developer['password'], $developer['salt']);
            return new AuthenticatableUser($developer);
        } else {
            $user = $query->where('mobile', $credentials['mobile'])->first();
            if ($user) {
                return $user;
            } else {
                throw ValidationException::withMessages([
                    'mobile' => ['用户不存在'],
                ]);
            }
        }
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        if (array_has($credentials, 'dingtalk_auth_code')) {
            return true;
        } else {
            $plain = $credentials['password'];
            return Encypt::password($plain, $user->salt) == $user->getAuthPassword();
        }
    }

    public function updateRememberToken(UserContract $user, $token)
    {
        //parent::updateRememberToken($user, $token);
    }
}