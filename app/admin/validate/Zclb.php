<?php 
/*
 module:		资产类别控制器
 create_time:	2022-12-30 08:28:43
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Zclb extends validate {


	protected $rule = [
		'zclb_name'=>['require'],
	];

	protected $message = [
		'zclb_name.require'=>'类别名称不能为空',
	];

	protected $scene  = [
		'add'=>['zclb_name'],
		'update'=>['zclb_name'],
	];



}

