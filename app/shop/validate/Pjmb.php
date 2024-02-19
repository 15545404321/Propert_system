<?php 
/*
 module:		票据模板控制器
 create_time:	2023-01-18 11:57:35
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Pjmb extends validate {


	protected $rule = [
		'pjgl_id'=>['require'],
		'pjmb_kuan'=>['require','regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
		'pjmb_gao'=>['require'],
		'pjgl_gzdy'=>['require'],
		'pimb_gzwz'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
	];

	protected $message = [
		'pjgl_id.require'=>'票据名称不能为空',
		'pjmb_kuan.require'=>'纸张宽度不能为空',
		'pjmb_kuan.regex'=>'纸张宽度格式错误',
		'pjmb_gao.require'=>'纸张高度不能为空',
		'pjgl_gzdy.require'=>'公章打印不能为空',
		'pimb_gzwz.regex'=>'水平位置格式错误',
	];

	protected $scene  = [
		'add'=>['pjgl_id','pjmb_kuan','pjmb_gao','pjgl_gzdy','pimb_gzwz'],
		'update'=>['pjgl_id','pjmb_kuan','pjmb_gao','pjgl_gzdy','pimb_gzwz'],
	];



}

