<?php 
/*
 module:		费用单位控制器
 create_time:	2022-12-13 11:53:16
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Fydw extends validate {


	protected $rule = [
		'fydw_name'=>['require'],
	];

	protected $message = [
		'fydw_name.require'=>'单位名称不能为空',
	];

	protected $scene  = [
		'add'=>['fydw_name'],
		'update'=>['fydw_name'],
	];



}

