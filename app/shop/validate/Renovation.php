<?php 
/*
 module:		首页装修控制器
 create_time:	2023-05-06 12:00:33
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Renovation extends validate {


	protected $rule = [
		'renovation_name'=>['require'],
		'renovation_image'=>['require'],
	];

	protected $message = [
		'renovation_name.require'=>'导航标题不能为空',
		'renovation_image.require'=>'导航图片不能为空',
	];

	protected $scene  = [
		'add'=>['renovation_name','renovation_image'],
		'update'=>['renovation_name','renovation_image'],
	];



}

