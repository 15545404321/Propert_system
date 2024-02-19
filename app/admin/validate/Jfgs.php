<?php 
/*
 module:		计费公式控制器
 create_time:	2022-12-29 09:14:00
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Jfgs extends validate {


	protected $rule = [
		'jfgs_name'=>['require'],
	];

	protected $message = [
		'jfgs_name.require'=>'公式名称不能为空',
	];

	protected $scene  = [
		'add'=>['jfgs_name'],
		'update'=>['jfgs_name'],
	];



}

