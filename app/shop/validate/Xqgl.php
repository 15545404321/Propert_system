<?php 
/*
 module:		项目管理控制器
 create_time:	2023-01-08 10:57:53
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Xqgl extends validate {


	protected $rule = [
		'xqgl_name'=>['require'],
	];

	protected $message = [
		'xqgl_name.require'=>'项目名称不能为空',
	];

	protected $scene  = [
		'add'=>['xqgl_name'],
		'update'=>['xqgl_name'],
	];



}

