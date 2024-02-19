<?php 
/*
 module:		计费方式列表控制器
 create_time:	2023-01-10 09:42:12
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Jffslb extends validate {


	protected $rule = [
		'fybz_name'=>['require'],
		'jfgs_id'=>['require'],
		'fybz_bzdj'=>['require'],
		'fybz_jfxs'=>['require'],
		'fybz_hzl'=>['require'],
	];

	protected $message = [
		'fybz_name.require'=>'计费名称不能为空',
		'jfgs_id.require'=>'计费公式不能为空',
		'fybz_bzdj.require'=>'标准单价不能为空',
		'fybz_jfxs.require'=>'计费系数不能为空',
		'fybz_hzl.require'=>'坏账率不能为空',
	];

	protected $scene  = [
		'add'=>['fybz_name','jfgs_id','fybz_bzdj','fybz_jfxs','fybz_hzl'],
		'update'=>['fybz_name','jfgs_id','fybz_bzdj','fybz_jfxs','fybz_hzl'],
	];



}

