<?php 
/*
 module:		收款方式控制器
 create_time:	2022-12-31 14:33:54
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Skfs extends validate {


	protected $rule = [
		'skfs_name'=>['require'],
	];

	protected $message = [
		'skfs_name.require'=>'方式名称不能为空',
	];

	protected $scene  = [
		'add'=>['skfs_name'],
		'update'=>['skfs_name'],
	];



}

