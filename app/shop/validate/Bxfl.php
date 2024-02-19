<?php 
/*
 module:		报修分类控制器
 create_time:	2023-01-09 10:00:13
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Bxfl extends validate {


	protected $rule = [
		'bxfl_name'=>['require'],
	];

	protected $message = [
		'bxfl_name.require'=>'分类名称不能为空',
	];

	protected $scene  = [
		'add'=>['bxfl_name'],
		'update'=>['bxfl_name'],
	];



}

