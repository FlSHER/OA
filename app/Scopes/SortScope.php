<?php
/**
 * Created by PhpStorm.
 * User: Fisher
 * Date: 2018/4/15 0015
 * Time: 10:11
 */

namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SortScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        return $builder->orderBy('sort', 'asc');
    }
}