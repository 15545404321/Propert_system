<?php 
/*
 module:		收银台控制器
 create_time:	2022-12-31 10:06:14
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Syt extends validate {


	protected $rule = [
		'fcxx_id'=>['require'],
		'syt_method'=>['require'],
		'zkgl_id'=>['require'],
		'syt_invoice'=>['require'],
		'syt_skje'=>['require'],
		'syt_zlje'=>['require'],
		'syt_bz'=>['require'],
	];

	protected $message = [
		'fcxx_id.require'=>'选择房间不能为空',
		'syt_method.require'=>'收款方式不能为空',
		'zkgl_id.require'=>'优惠方式不能为空',
		'syt_invoice.require'=>'发票编号不能为空',
		'syt_skje.require'=>'收款金额不能为空',
		'syt_zlje.require'=>'找零金额不能为空',
		'syt_bz.require'=>'备注信息不能为空',
	];

	protected $scene  = [
		'add'=>['fcxx_id','syt_method','zkgl_id','syt_invoice','syt_skje','syt_zlje','syt_bz'],
		'update'=>['fcxx_id','syt_method','zkgl_id','syt_invoice','syt_skje','syt_zlje','syt_bz'],
	];



}

