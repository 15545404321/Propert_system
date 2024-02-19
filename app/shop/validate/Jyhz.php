<?php 
/*
 module:		交易汇总控制器
 create_time:	2023-02-18 10:03:48
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Jyhz extends validate {


	protected $rule = [
		'yssj_fymc'=>['require'],
		'yssj_cwyf'=>['require'],
		'yssj_kstime'=>['require'],
		'yssj_jztime'=>['require'],
		'yssj_fydj'=>['require'],
		'yssj_ysje'=>['require'],
		'fylx_id'=>['require'],
		'fybz_id'=>['require'],
		'yssj_fksj'=>['require'],
	];

	protected $message = [
		'yssj_fymc.require'=>'费用名称不能为空',
		'yssj_cwyf.require'=>'财务月份不能为空',
		'yssj_kstime.require'=>'开始日期不能为空',
		'yssj_jztime.require'=>'截至日期不能为空',
		'yssj_fydj.require'=>'费用单价不能为空',
		'yssj_ysje.require'=>'应收金额不能为空',
		'fylx_id.require'=>'费用类型不能为空',
		'fybz_id.require'=>'费用标准不能为空',
		'yssj_fksj.require'=>'付款时间不能为空',
	];

	protected $scene  = [
	];



}

