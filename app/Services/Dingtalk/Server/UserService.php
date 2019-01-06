<?php 

namespace App\Services\Dingtalk\Server;

class UserService extends DingtalkAbstract
{
	/**
     * Get the provider.
     *
     * @return string
     */
    public function provider(): string
    {
        return 'user';
    }

	/**
     * @param string $userId
     *
     * @return array
     */
    public function get(string $userId)
    {
        return $this->httpGet('user/get', ['userid' => $userId]);
    }

    /**
     * Create a new user.
     *
     * @param array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->httpPostJson('user/create', $params);
    }

    /**
     * Update an exist user.
     *
     * @param array $params
     *
     * @return array
     */
    public function update(array $params)
    {
        return $this->httpPostJson('user/update', $params);
    }

    /**
     * @param array|string $userId
     *
     * @return array
     */
    public function delete($userId)
    {
        if (is_array($userId)) {
            return $this->httpPostJson('user/batchdelete', ['useridlist' => $userId]);
        }

        return $this->httpGet('user/delete', $userId);
    }

    /**
     * @return array
     */
    public function admin()
    {
        return $this->httpGet('user/get_admin');
    }

    /**
     * @param string $code
     *
     * @return array
     */
    public function getUserInfo(string $code)
    {
        return $this->httpGet('user/getuserinfo', ['code' => $code]);
    }
}