<?php 
/*
 module:		报修信息控制器
 create_time:	2023-03-23 11:23:51
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Bxxx extends validate {


	protected $rule = [
		'bxxx_miaoshu'=>['require'],
		'bxxx_start'=>['require'],
	];

	protected $message = [
		'bxxx_miaoshu.require'=>'报修描述不能为空',
		'bxxx_start.require'=>'处理状态不能为空',
	];

	protected $scene  = [
		'add'=>['bxxx_miaoshu'],
		'update'=>['bxxx_miaoshu','bxxx_start'],
		'fankui'=>[''],
		'yezhufankui'=>['bxxx_start'],
	];



}

