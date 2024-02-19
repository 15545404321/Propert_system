<?php 
/*
 module:		费用类型控制器
 create_time:	2022-12-13 10:17:12
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Fylx extends validate {


	protected $rule = [
		'fylx_name'=>['require'],
	];

	protected $message = [
		'fylx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['fylx_name'],
		'update'=>['fylx_name'],
	];



}

