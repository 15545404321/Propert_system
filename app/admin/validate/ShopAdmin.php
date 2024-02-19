<?php 
/*
 module:		物业账号控制器
 create_time:	2023-01-20 12:15:17
 author:		
 contact:		
*/

namespace app\admin\validate;
use think\validate;

class ShopAdmin extends validate {


	protected $rule = [
		'cname'=>['require'],
		'account'=>['require'],
		'password'=>['require'],
	];

	protected $message = [
		'cname.require'=>'操作人员不能为空',
		'account.require'=>'用户账号不能为空',
		'password.require'=>'用户密码不能为空',
	];

	protected $scene  = [
		'add'=>['cname','account','password'],
		'update'=>['cname','account'],
	];



}

