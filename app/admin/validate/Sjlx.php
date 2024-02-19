<?php 
/*
 module:		数据类型控制器
 create_time:	2023-01-06 16:56:23
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Sjlx extends validate {


	protected $rule = [
		'sjlx_name'=>['require'],
	];

	protected $message = [
		'sjlx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['sjlx_name'],
		'update'=>['sjlx_name'],
	];



}

