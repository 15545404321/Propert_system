<?php 
/*
 module:		取整方式控制器
 create_time:	2022-12-14 14:17:34
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Qzfs extends validate {


	protected $rule = [
		'qzfs_name'=>['require'],
	];

	protected $message = [
		'qzfs_name.require'=>'方式名称不能为空',
	];

	protected $scene  = [
		'add'=>['qzfs_name'],
		'update'=>['qzfs_name'],
	];



}

