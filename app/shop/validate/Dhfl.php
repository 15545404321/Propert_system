<?php 
/*
 module:		电话分类控制器
 create_time:	2023-01-09 09:56:30
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Dhfl extends validate {


	protected $rule = [
		'dhfl_name'=>['require'],
	];

	protected $message = [
		'dhfl_name.require'=>'分类名称不能为空',
	];

	protected $scene  = [
		'add'=>['dhfl_name'],
		'update'=>['dhfl_name'],
	];



}

