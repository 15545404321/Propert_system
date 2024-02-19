<?php 
/*
 module:		开通日志方法控制器
 create_time:	2023-01-26 10:58:48
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Rizhi extends validate {


	protected $rule = [
		'rz_fangfa'=>['require'],
	];

	protected $message = [
		'rz_fangfa.require'=>'方法名称不能为空',
	];

	protected $scene  = [
		'add'=>['rz_fangfa'],
		'update'=>['rz_fangfa'],
	];



}

