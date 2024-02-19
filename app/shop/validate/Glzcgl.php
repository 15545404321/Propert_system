<?php 
/*
 module:		关联资产管理控制器
 create_time:	2022-12-26 08:38:38
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Glzcgl extends validate {


	protected $rule = [
		'zclx_id'=>['require'],
	];

	protected $message = [
		'zclx_id.require'=>'资产类型不能为空',
	];

	protected $scene  = [
		'add'=>['zclx_id'],
		'update'=>['zclx_id'],
	];



}

