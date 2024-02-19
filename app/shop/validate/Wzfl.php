<?php 
/*
 module:		文章分类控制器
 create_time:	2023-01-09 10:05:58
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Wzfl extends validate {


	protected $rule = [
		'wzfl_name'=>['require'],
	];

	protected $message = [
		'wzfl_name.require'=>'分类名称不能为空',
	];

	protected $scene  = [
		'add'=>['wzfl_name'],
		'update'=>['wzfl_name'],
	];



}

