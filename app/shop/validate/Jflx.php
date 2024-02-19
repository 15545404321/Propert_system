<?php 
/*
 module:		计费类型控制器
 create_time:	2022-12-13 12:04:36
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Jflx extends validate {


	protected $rule = [
		'jflx_name'=>['require'],
	];

	protected $message = [
		'jflx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['jflx_name'],
		'update'=>['jflx_name'],
	];



}

