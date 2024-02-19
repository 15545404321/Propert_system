<?php 
/*
 module:		停车场地控制器
 create_time:	2023-01-08 17:19:16
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Tccd extends validate {


	protected $rule = [
		'tccd_name'=>['require'],
	];

	protected $message = [
		'tccd_name.require'=>'场地名称不能为空',
	];

	protected $scene  = [
		'add'=>['tccd_name'],
		'update'=>['tccd_name'],
	];



}

