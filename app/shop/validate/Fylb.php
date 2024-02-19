<?php 
/*
 module:		费用类别控制器
 create_time:	2022-12-14 15:41:41
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Fylb extends validate {


	protected $rule = [
		'fylb_name'=>['require'],
	];

	protected $message = [
		'fylb_name.require'=>'类别名称不能为空',
	];

	protected $scene  = [
		'add'=>['fylb_name'],
		'update'=>['fylb_name'],
	];



}

