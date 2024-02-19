<?php 
/*
 module:		客户类别控制器
 create_time:	2022-12-09 16:09:26
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Khlb extends validate {


	protected $rule = [
		'khlb_name'=>['require'],
	];

	protected $message = [
		'khlb_name.require'=>'类别名称不能为空',
	];

	protected $scene  = [
		'add'=>['khlb_name'],
		'update'=>['khlb_name'],
	];



}

