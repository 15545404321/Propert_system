<?php 
/*
 module:		费用定义控制器
 create_time:	2023-01-16 14:25:22
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Fydy extends validate {


	protected $rule = [
		'fylx_id'=>['require'],
		'fydy_name'=>['require'],
		'fylb_id'=>['require'],
		'fydw_id'=>['require'],
		'jflx_id'=>['require'],
		'qzfs_id'=>['require'],
	];

	protected $message = [
		'fylx_id.require'=>'选择类型不能为空',
		'fydy_name.require'=>'费用名称不能为空',
		'fylb_id.require'=>'选择类别不能为空',
		'fydw_id.require'=>'费用单位不能为空',
		'jflx_id.require'=>'计费类型不能为空',
		'qzfs_id.require'=>'取整方式不能为空',
	];

	protected $scene  = [
		'add'=>['fylx_id','fydy_name','fylb_id','fydw_id','jflx_id','qzfs_id'],
		'update'=>['fylx_id','fydy_name','fylb_id','fydw_id','jflx_id','qzfs_id'],
	];



}

