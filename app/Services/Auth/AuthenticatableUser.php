<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/1/7 0007
 * Time: 15:44
 */

namespace App\Services\Auth;


use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Auth\Authenticatable;
use Laravel\Passport\HasApiTokens;
use ArrayAccess;

class AuthenticatableUser implements UserContract, ArrayAccess
{
    use Authenticatable, HasApiTokens;

    protected $item;

    public function __construct($attribute)
    {
        $this->items = $attribute;
    }

    public function getAuthIdentifierName()
    {
        return 'staff_sn';
    }

    public function __get($key)
    {
        return $this->items[$key];
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function getKey()
    {
        return $this->getAuthIdentifier();
    }
}