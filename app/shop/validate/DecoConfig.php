<?php 
/*
 module:		装修配置控制器
 create_time:	2023-05-05 14:21:08
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class DecoConfig extends validate {


	protected $rule = [
		'decoconfig_name'=>['require'],
	];

	protected $message = [
		'decoconfig_name.require'=>'页面名称不能为空',
	];

	protected $scene  = [
		'add'=>['decoconfig_name'],
		'update'=>['decoconfig_name'],
	];



}

