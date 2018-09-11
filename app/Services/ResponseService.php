<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4/004
 * Time: 13:52
 */

namespace App\Services;


class ResponseService
{

    protected $statusCode = [
        'get'=>200,//OK - [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）。
        'post'=>201,//CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
        'put'=>201,//CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
        'patch'=>201,//CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
        'delete'=>204,//NO CONTENT - [DELETE]：用户删除数据成功。
        'invalidRequest'=>400,//INVALID REQUEST - [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。
        'unauthorized'=>401,//Unauthorized - [*]：表示用户没有权限（令牌、用户名、密码错误）。
        'forbidden'=>403,//Forbidden - [*] 表示用户得到授权（与401错误相对），但是访问是被禁止的。
        'notFound'=>404,//NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
        'gone'=>410,//Gone -[GET]：用户请求的资源被永久删除，且不会再得到的。
    ];

    /**
     * 服务器成功返回用户请求的数据
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function get($data)
    {
        return response($data,$this->statusCode[__FUNCTION__]);
    }

    /**
     * 用户新建或修改数据成功。
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function post($data)
    {
        return response($data,$this->statusCode[__FUNCTION__]);
    }
    /**
     * 用户新建或修改数据成功。
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function put($data)
    {
        return response($data,$this->statusCode[__FUNCTION__]);
    }
    /**
     * 用户新建或修改数据成功。
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function patch($data)
    {
        return response($data,$this->statusCode[__FUNCTION__]);
    }

    /**
     * 用户删除数据成功
     * @param null $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function delete($data = null)
    {
        return response($data,$this->statusCode[__FUNCTION__]);
    }

    /**
     * 用户发出的请求有错误，服务器没有进行新建或修改数据的操作
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function invalidRequest($error)
    {
        return response(['error'=>$error],$this->statusCode[__FUNCTION__]);
    }


    /**
     * 表示用户没有权限（令牌、用户名、密码错误）。
     * @param $error
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function unauthorized($error)
    {
        return response(['error'=>$error],$this->statusCode[__FUNCTION__]);
    }

    /**
     * 表示用户得到授权（与401错误相对），但是访问是被禁止的。
     * @param $error
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function forbidden($error)
    {
        return response(['error'=>$error],$this->statusCode[__FUNCTION__]);
    }
    /**
     * 用户发出的请求针对的是不存在的记录，服务器没有进行操作
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function notFound($error)
    {
        return response(['error'=>$error],$this->statusCode[__FUNCTION__]);
    }

    /**
     * 用户请求的资源被永久删除，且不会再得到的
     * @param $error
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function gone($error)
    {
        return response(['error'=>$error],$this->statusCode[__FUNCTION__]);
    }

}