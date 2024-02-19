<?php 
/*
 module:		功能角色控制器
 create_time:	2023-01-20 15:26:33
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class ShopRole extends validate {


	protected $rule = [
		'name'=>['require'],
	];

	protected $message = [
		'name.require'=>'级别角色不能为空',
	];

	protected $scene  = [
		'add'=>['name'],
		'update'=>['name'],
		'getRoleAccess'=>['name'],
	];



}

