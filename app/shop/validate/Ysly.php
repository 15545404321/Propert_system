<?php 
/*
 module:		领用记录控制器
 create_time:	2023-01-06 16:02:08
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Ysly extends validate {


	protected $rule = [
		'ys_state'=>['require'],
	];

	protected $message = [
		'ys_state.require'=>'使用状态不能为空',
	];

	protected $scene  = [
		'add'=>[''],
		'update'=>['ys_state'],
	];



}

