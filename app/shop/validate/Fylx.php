<?php 
/*
 module:		费用定义控制器
 create_time:	2023-01-09 07:26:02
 author:		
 contact:		
*/

namespace app\shop\validate;
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

