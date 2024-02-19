<?php 
/*
 module:		仪表类型控制器
 create_time:	2022-12-12 10:33:31
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Yblx extends validate {


	protected $rule = [
		'yblx_name'=>['require'],
	];

	protected $message = [
		'yblx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['yblx_name'],
		'update'=>['yblx_name'],
	];



}

