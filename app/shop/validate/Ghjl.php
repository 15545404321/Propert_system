<?php 
/*
 module:		过户记录控制器
 create_time:	2023-01-26 11:43:38
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Ghjl extends validate {


	protected $rule = [
		'member_id'=>['require'],
		'member_idb'=>['require'],
		'ghjl_time'=>['require'],
		'ghjl_jiesuan'=>['require'],
	];

	protected $message = [
		'member_id.require'=>'原始房主不能为空',
		'member_idb.require'=>'新任房主不能为空',
		'ghjl_time.require'=>'过户时间不能为空',
		'ghjl_jiesuan.require'=>'费用结算不能为空',
	];

	protected $scene  = [
		'add'=>['member_id','member_idb','ghjl_time','ghjl_jiesuan'],
		'tuihui'=>[''],
	];



}

