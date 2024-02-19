<?php 
/*
 module:		证件类型控制器
 create_time:	2022-12-09 16:09:23
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Zjlx extends validate {


	protected $rule = [
		'zjlx_name'=>['require'],
	];

	protected $message = [
		'zjlx_name.require'=>'类型名称不能为空',
	];

	protected $scene  = [
		'add'=>['zjlx_name'],
		'update'=>['zjlx_name'],
	];



}

