<?php 
/*
 module:		客户类型控制器
 create_time:	2022-12-16 16:29:13
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Khlx extends validate {


	protected $rule = [
		'khlx_name'=>['require'],
	];

	protected $message = [
		'khlx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['khlx_name'],
		'update'=>['khlx_name'],
	];



}

