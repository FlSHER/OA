<?php 

namespace App\Services\Dingtalk\Server;

class DeptService extends DingtalkAbstract
{

    /**
     * Get the provider.
     *
     * @return string
     */
    public function provider(): string
    {
        return 'department';
    }

    /**
     * @param  string $id
     * 
     * @return array
     */
    public function get(string $id)
    {
        return $this->httpGet('department/get', compact('id'));
    }

    /**
     * @param string $id
     * @param bool $fetch_child
     * 
     * @return array
     */
    public function list(string $id = '1', bool $fetch_child = false)
    {
        return $this->httpGet('department/list', [
            'id' => $id,
            'fetch_child' => $fetch_child,
        ]);
    }

    /**
     * Create a new department.
     *
     * @param array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->httpPostJson('department/create', $params);
    }

    /**
     * Update an exist department.
     *
     * @param array $params
     *
     * @return array
     */
    public function update(array $params)
    {
        return $this->httpPostJson('department/update', $params);
    }

    /**
     * @param array|string $userId
     *
     * @return array
     */
    public function delete($id)
    {
        return $this->httpGet('department/delete', compact('id'));
    }

    /**
     * @param number $onlyActive
     *
     * @return array
     */
    public function getUserCount($onlyActive = 0)
    {
        return $this->httpGet('user/get_org_user_count', compact('onlyActive'));
    }

}