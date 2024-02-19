<?php 
/*
 module:		员工管理控制器
 create_time:	2023-01-06 11:13:05
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class ShopAdmin extends validate {


	protected $rule = [
		'cname'=>['require'],
		'xqgl_id'=>['require'],
		'zzgl_id'=>['require'],
		'gwgl_id'=>['require'],
		'account'=>['require','unique:shop_admin'],
		'tel'=>['require','regex'=>'/^[0-9]*$/'],
	];

	protected $message = [
		'cname.require'=>'人员姓名不能为空',
		'xqgl_id.require'=>'开资项目不能为空',
		'zzgl_id.require'=>'所属部门不能为空',
		'gwgl_id.require'=>'所属岗位不能为空',
		'account.require'=>'用户账号不能为空',
		'account.unique'=>'用户账号已经存在',
		'tel.require'=>'用户手机不能为空',
		'tel.regex'=>'用户手机格式错误',
	];

	protected $scene  = [
		'add'=>['cname','xqgl_id','zzgl_id','gwgl_id','account','tel'],
		'update'=>['cname','xqgl_id','zzgl_id','gwgl_id','account','tel'],
	];



}

