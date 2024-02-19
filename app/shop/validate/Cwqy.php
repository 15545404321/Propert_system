<?php 
/*
 module:		车位区域控制器
 create_time:	2023-01-08 17:18:57
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Cwqy extends validate {


	protected $rule = [
		'cwqy_name'=>['require'],
	];

	protected $message = [
		'cwqy_name.require'=>'区域名称不能为空',
	];

	protected $scene  = [
		'add'=>['cwqy_name'],
		'update'=>['cwqy_name'],
	];



}

