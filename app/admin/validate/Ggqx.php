<?php 
/*
 module:		公共权限控制器
 create_time:	2023-01-20 16:14:40
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Ggqx extends validate {


	protected $rule = [
		'ggqx_url'=>['require'],
	];

	protected $message = [
		'ggqx_url.require'=>'权限地址不能为空',
	];

	protected $scene  = [
		'add'=>['ggqx_url'],
		'update'=>['ggqx_url'],
	];



}

