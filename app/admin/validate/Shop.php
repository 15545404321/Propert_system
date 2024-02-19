<?php 
/*
 module:		物业管理控制器
 create_time:	2023-02-14 09:46:28
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class Shop extends validate {


	protected $rule = [
		'shop_name'=>['require'],
		'shop_tel'=>['regex'=>'/^1[3456789]\d{9}$/'],
		'shop_email'=>['regex'=>'/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/'],
		'start_date'=>['require'],
		'end_date'=>['require'],
		'goumai'=>['require'],
		'restrict_num'=>['require','regex'=>'/^[0-9]*$/'],
	];

	protected $message = [
		'shop_name.require'=>'公司名称不能为空',
		'shop_tel.regex'=>'联系电话格式错误',
		'shop_email.regex'=>'公司邮箱格式错误',
		'start_date.require'=>'开始日期不能为空',
		'end_date.require'=>'到期日期不能为空',
		'goumai.require'=>'购买功能不能为空',
		'restrict_num.require'=>'小区上限不能为空',
		'restrict_num.regex'=>'小区上限格式错误',
	];

	protected $scene  = [
		'add'=>['shop_name','shop_tel','shop_email','start_date','end_date','goumai','restrict_num'],
		'update'=>['shop_name','shop_tel','shop_email','start_date','end_date','goumai','restrict_num'],
	];



}

