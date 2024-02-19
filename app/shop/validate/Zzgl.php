<?php 
/*
 module:		部门管理控制器
 create_time:	2023-01-09 14:44:32
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Zzgl extends validate {


	protected $rule = [
		'zzgl_bmmc'=>['require'],
		'xqgl_id'=>['require'],
	];

	protected $message = [
		'zzgl_bmmc.require'=>'部门名称不能为空',
		'xqgl_id.require'=>'所属项目不能为空',
	];

	protected $scene  = [
		'add'=>['zzgl_bmmc','xqgl_id'],
		'update'=>['zzgl_bmmc','xqgl_id'],
		'batupdate'=>['xqgl_id'],
	];



}

