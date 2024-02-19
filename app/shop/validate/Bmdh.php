<?php 
/*
 module:		电话号码控制器
 create_time:	2023-02-10 10:03:20
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Bmdh extends validate {


	protected $rule = [
		'bmdh_title'=>['require'],
		'bmdh_tel'=>['require','regex'=>'/^[0-9]*$/'],
		'dhfl_id'=>['require'],
	];

	protected $message = [
		'bmdh_title.require'=>'商家名称不能为空',
		'bmdh_tel.require'=>'电话号码不能为空',
		'bmdh_tel.regex'=>'电话号码格式错误',
		'dhfl_id.require'=>'电话分类不能为空',
	];

	protected $scene  = [
		'add'=>['bmdh_title','bmdh_tel','dhfl_id'],
		'update'=>['bmdh_title','bmdh_tel','dhfl_id'],
	];



}

