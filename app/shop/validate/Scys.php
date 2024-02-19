<?php 
/*
 module:		应收管理控制器
 create_time:	2023-01-10 09:13:59
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Scys extends validate {


	protected $rule = [
		'scys_ksyf'=>['require'],
		'scys_jsyf'=>['require'],
		'jflx_id'=>['require'],
		'fydy_id'=>['require'],
	];

	protected $message = [
		'scys_ksyf.require'=>'开始月份不能为空',
		'scys_jsyf.require'=>'结束月份不能为空',
		'jflx_id.require'=>'建筑类型不能为空',
		'fydy_id.require'=>'费用种类不能为空',
	];

	protected $scene  = [
		'add'=>['scys_ksyf','scys_jsyf','jflx_id','fydy_id'],
		'update'=>['scys_ksyf','scys_jsyf','jflx_id','fydy_id'],
	];



}

