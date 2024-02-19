<?php 
/*
 module:		钥匙分类控制器
 create_time:	2022-12-31 16:52:33
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Ysfl extends validate {


	protected $rule = [
		'ysfl_name'=>['require'],
	];

	protected $message = [
		'ysfl_name.require'=>'名称/分类不能为空',
	];

	protected $scene  = [
		'add'=>['ysfl_name'],
		'update'=>['ysfl_name'],
	];



}

