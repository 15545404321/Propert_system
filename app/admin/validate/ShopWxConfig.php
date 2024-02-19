<?php 
/*
 module:		微信菜单控制器
 create_time:	2023-02-14 09:55:02
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class ShopWxConfig extends validate {


	protected $rule = [
		'title'=>['require'],
		'type'=>['require'],
	];

	protected $message = [
		'title.require'=>'导航名称不能为空',
		'type.require'=>'菜单类型不能为空',
	];

	protected $scene  = [
		'add'=>['title','type'],
		'update'=>['title','type'],
	];



}

