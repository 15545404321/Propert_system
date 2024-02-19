<?php 
/*
 module:		资产台账控制器
 create_time:	2023-01-06 16:02:34
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Zcml extends validate {


	protected $rule = [
		'zcml_type'=>['require'],
	];

	protected $message = [
		'zcml_type.require'=>'资产性质不能为空',
	];

	protected $scene  = [
		'add'=>['zcml_type'],
		'update'=>['zcml_type'],
	];



}

