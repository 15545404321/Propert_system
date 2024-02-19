<?php 
/*
 module:		押金台账控制器
 create_time:	2023-01-16 15:19:19
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Yajin extends validate {


	protected $rule = [
		'zjys_bcys'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
	];

	protected $message = [
		'zjys_bcys.regex'=>'本次应收格式错误',
	];

	protected $scene  = [
		'add'=>['zjys_bcys'],
		'update'=>[''],
	];



}

