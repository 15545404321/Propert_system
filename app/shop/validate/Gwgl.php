<?php 
/*
 module:		岗位管理控制器
 create_time:	2023-10-13 16:33:40
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Gwgl extends validate {


	protected $rule = [
		'gwgl_gwmc'=>['require'],
		'xqgl_id'=>['require'],
	];

	protected $message = [
		'gwgl_gwmc.require'=>'岗位名称不能为空',
		'xqgl_id.require'=>'所属项目不能为空',
	];

	protected $scene  = [
		'add'=>['gwgl_gwmc','xqgl_id'],
		'update'=>['gwgl_gwmc','xqgl_id'],
	];



}

