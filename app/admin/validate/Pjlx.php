<?php 
/*
 module:		票据类型控制器
 create_time:	2023-01-18 11:16:37
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Pjlx extends validate {


	protected $rule = [
		'pjlx_name'=>['require'],
	];

	protected $message = [
		'pjlx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['pjlx_name'],
		'update'=>['pjlx_name'],
	];



}

