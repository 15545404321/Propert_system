<?php 
/*
 module:		费用分配控制器
 create_time:	2023-01-09 08:38:12
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Fplb extends validate {


	protected $rule = [
		'fylx_id'=>['require'],
		'fydy_name'=>['require'],
		'fydw_id'=>['require'],
	];

	protected $message = [
		'fylx_id.require'=>'费用类型不能为空',
		'fydy_name.require'=>'费用名称不能为空',
		'fydw_id.require'=>'费用单位不能为空',
	];

	protected $scene  = [
		'add'=>['fylx_id','fydy_name','fydw_id'],
		'update'=>['fylx_id','fydy_name','fydw_id'],
	];



}

