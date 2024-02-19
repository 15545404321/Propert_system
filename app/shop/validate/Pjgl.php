<?php 
/*
 module:		票据管理控制器
 create_time:	2023-01-18 11:56:05
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Pjgl extends validate {


	protected $rule = [
		'pjlx_id'=>['require'],
		'pjlx_pid'=>['require'],
		'pjgl_name'=>['require'],
		'pjgl_qsbm'=>['require'],
	];

	protected $message = [
		'pjlx_id.require'=>'票据类型不能为空',
		'pjlx_pid.require'=>'票据模板不能为空',
		'pjgl_name.require'=>'票据名称不能为空',
		'pjgl_qsbm.require'=>'起始编码不能为空',
	];

	protected $scene  = [
		'add'=>['pjlx_id','pjlx_pid','pjgl_name','pjgl_qsbm'],
		'update'=>['pjlx_id','pjlx_pid','pjgl_name','pjgl_qsbm'],
	];



}

