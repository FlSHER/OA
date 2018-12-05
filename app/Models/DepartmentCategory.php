<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentCategory extends Model
{
	use SoftDeletes;
	
	/**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
	protected $fillable = [
		'name',
		'fields',
		'is_locked',
	];

	/**
	 * 自动转换字段格式.
	 * 
	 * @var array
	 */
	protected $casts = [
		'fields' => 'array',
	];

	/**
	 * 分类下的部门.
	 * 
	 * @return hasMany
	 */
	public function department()
	{
		return $this->hasMany(Department::class, 'cate_id', 'id');
	}
}
