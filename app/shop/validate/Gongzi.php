<?php 
/*
 module:		工资记录控制器
 create_time:	2023-01-18 14:31:51
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Gongzi extends validate {


	protected $rule = [
		'gz_jine'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
		'xzpici_id'=>['require'],
	];

	protected $message = [
		'gz_jine.regex'=>'发放金额格式错误',
		'xzpici_id.require'=>'发放批次不能为空',
	];

	protected $scene  = [
		'add'=>['gz_jine','xzpici_id'],
		'update'=>['gz_jine','xzpici_id'],
		'batupkq'=>[''],
		'batupkj'=>[''],
		'batupdate'=>[''],
	];



}

