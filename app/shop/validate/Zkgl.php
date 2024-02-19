<?php 
/*
 module:		折扣管理控制器
 create_time:	2022-12-19 00:06:14
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Zkgl extends validate {


	protected $rule = [
		'zkgl_name'=>['require'],
		'zkgl_zks'=>['require'],
	];

	protected $message = [
		'zkgl_name.require'=>'优惠名称不能为空',
		'zkgl_zks.require'=>'优惠折扣不能为空',
	];

	protected $scene  = [
		'add'=>['zkgl_name','zkgl_zks'],
		'update'=>['zkgl_name','zkgl_zks'],
	];



}

