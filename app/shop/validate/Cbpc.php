<?php 
/*
 module:		抄表管理控制器
 create_time:	2023-01-28 12:59:22
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Cbpc extends validate {


	protected $rule = [
		'cbpc_cwyf'=>['require'],
		'cbpc_kstime'=>['require'],
		'cbpc_jstime'=>['require'],
		'louyu_id'=>['require'],
	];

	protected $message = [
		'cbpc_cwyf.require'=>'财务月份不能为空',
		'cbpc_kstime.require'=>'开始日期不能为空',
		'cbpc_jstime.require'=>'结束日期不能为空',
		'louyu_id.require'=>'楼座编号不能为空',
	];

	protected $scene  = [
		'add'=>['cbpc_cwyf','cbpc_kstime','cbpc_jstime','louyu_id'],
		'update'=>['cbpc_cwyf','cbpc_kstime','cbpc_jstime'],
	];



}

