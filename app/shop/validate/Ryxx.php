<?php 
/*
 module:		员工信息控制器
 create_time:	2023-01-03 21:29:39
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Ryxx extends validate {


	protected $rule = [
		'shop_admin_id'=>['unique:ryxx'],
	];

	protected $message = [
		'shop_admin_id.unique'=>'工作人员已经存在',
	];

	protected $scene  = [
		'add'=>['shop_admin_id'],
		'update'=>['shop_admin_id'],
	];



}

