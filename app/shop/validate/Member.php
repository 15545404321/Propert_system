<?php 
/*
 module:		客户信息控制器
 create_time:	2023-01-13 08:04:18
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Member extends validate {


	protected $rule = [
		'member_name'=>['require'],
		'member_tel'=>['regex'=>'/^1[3456789]\d{9}$/'],
		'member_yucun'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
		'member_yingshou'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
	];

	protected $message = [
		'member_name.require'=>'客户名称不能为空',
		'member_tel.regex'=>'客户手机格式错误',
		'member_yucun.regex'=>'预存金额格式错误',
		'member_yingshou.regex'=>'应收金额格式错误',
	];

	protected $scene  = [
		'add'=>['member_name','member_tel'],
		'update'=>['member_name','member_tel'],
		'glfangchan'=>[''],
		'glchewei'=>[''],
		'glcar'=>[''],
	];



}

