<?php 
/*
 module:		临时应收控制器
 create_time:	2023-01-10 12:45:34
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Lsys extends validate {


	protected $rule = [
		'lsys_kstime'=>['require'],
		'lsys_jstime'=>['require'],
		'jflx_id'=>['require'],
		'fydy_id'=>['require'],
		'lsys_ysje'=>['require','regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
	];

	protected $message = [
		'lsys_kstime.require'=>'开始时间不能为空',
		'lsys_jstime.require'=>'结束时间不能为空',
		'jflx_id.require'=>'建筑类型不能为空',
		'fydy_id.require'=>'费用类型不能为空',
		'lsys_ysje.require'=>'应收金额不能为空',
		'lsys_ysje.regex'=>'应收金额格式错误',
	];

	protected $scene  = [
		'add'=>['lsys_kstime','lsys_jstime','jflx_id','fydy_id','lsys_ysje'],
		'update'=>['lsys_kstime','lsys_jstime','jflx_id','fydy_id','lsys_ysje'],
	];



}

