<?php 
/*
 module:		角色管理控制器
 create_time:	2022-09-06 17:14:45
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Role extends validate {


	protected $rule = [
		'name'=>['require'],
	];

	protected $message = [
		'name.require'=>'角色名称不能为空',
	];

	protected $scene  = [
		'add'=>['name'],
		'update'=>['name'],
	];



}

