<?php 
/*
 module:		资产类型控制器
 create_time:	2022-12-17 09:30:12
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Zclx extends validate {


	protected $rule = [
		'zclx_name'=>['require'],
	];

	protected $message = [
		'zclx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['zclx_name'],
		'update'=>['zclx_name'],
	];



}

