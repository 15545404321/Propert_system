<?php 
/*
 module:		首页模块控制器
 create_time:	2023-05-05 14:48:44
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class IndexModule extends validate {


	protected $rule = [
		'indexmodule_name'=>['require'],
	];

	protected $message = [
		'indexmodule_name.require'=>'模块名称不能为空',
	];

	protected $scene  = [
		'add'=>['indexmodule_name'],
		'update'=>['indexmodule_name'],
	];



}

