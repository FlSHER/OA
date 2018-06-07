<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/1/6 0006
 * Time: 14:48
 */

namespace App\Services\Auth;


use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Encypt;

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
                return parent::retrieveById($identifier)->toArray();
            });
        }
        return new AuthenticatableUser($attribute);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $developer = config('auth.developer');
        if ($credentials['mobile'] == $developer['username']) {
            $developer['password'] = Encypt::password($developer['password'], $developer['salt']);
            return new AuthenticatableUser($developer);
        } else {
            $credentials['is_active'] = 1;
            return parent::retrieveByCredentials($credentials);
        }
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];
        return Encypt::password($plain, $user->salt) == $user->getAuthPassword();
    }

    public function updateRememberToken(UserContract $user, $token)
    {
        //parent::updateRememberToken($user, $token);
    }
}