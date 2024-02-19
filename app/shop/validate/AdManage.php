<?php 
/*
 module:		广告管理控制器
 create_time:	2023-05-15 10:22:29
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class AdManage extends validate {


	protected $rule = [
		'admanage_pic'=>['require'],
	];

	protected $message = [
		'admanage_pic.require'=>'广告图片不能为空',
	];

	protected $scene  = [
		'add'=>['admanage_pic'],
		'update'=>['admanage_pic'],
	];



}

