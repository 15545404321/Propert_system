<?php 
/*
 module:		物业信息控制器
 create_time:	2023-01-18 19:01:37
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Shop extends validate {


	protected $rule = [
		'shop_name'=>['require'],
		'shop_address'=>['require'],
		'shop_range'=>['require'],
		'shop_xlr'=>['require'],
		'shop_tel'=>['require','regex'=>'/^1[3456789]\d{9}$/'],
		'shop_email'=>['regex'=>'/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/'],
		'start_date'=>['require'],
		'end_date'=>['require'],
		'restrict_num'=>['require','regex'=>'/^[0-9]*$/'],
		'shop_skdw'=>['require'],
	];

	protected $message = [
		'shop_name.require'=>'企业名称不能为空',
		'shop_address.require'=>'所在城市不能为空',
		'shop_range.require'=>'详细地址不能为空',
		'shop_xlr.require'=>'　总经理不能为空',
		'shop_tel.require'=>'联系电话不能为空',
		'shop_tel.regex'=>'联系电话格式错误',
		'shop_email.regex'=>'企业邮箱格式错误',
		'start_date.require'=>'开始日期不能为空',
		'end_date.require'=>'到期日期不能为空',
		'restrict_num.require'=>'项目上限不能为空',
		'restrict_num.regex'=>'项目上限格式错误',
		'shop_skdw.require'=>'收款单位不能为空',
	];

	protected $scene  = [
		'add'=>['shop_name','shop_address','shop_range','shop_xlr','shop_tel','shop_email','start_date','end_date','restrict_num','shop_skdw'],
		'update'=>['shop_name','shop_address','shop_range','shop_xlr','shop_tel','shop_email','shop_skdw'],
	];



}

