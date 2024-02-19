<?php 
/*
 module:		微信配置控制器
 create_time:	2023-02-09 11:31:00
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Wxpz extends validate {


	protected $rule = [
		'app_id'=>['require'],
		'secret'=>['require'],
		'mch_id'=>['require'],
		'pay_sign_key'=>['require'],
		'apiclient_cert'=>['require'],
		'apiclient_key'=>['require'],
	];

	protected $message = [
		'app_id.require'=>'APPID不能为空',
		'secret.require'=>'Secret不能为空',
		'mch_id.require'=>'支付商户不能为空',
		'pay_sign_key.require'=>'支付秘钥不能为空',
		'apiclient_cert.require'=>'支付证书不能为空',
		'apiclient_key.require'=>'证书密钥不能为空',
	];

	protected $scene  = [
		'add'=>['app_id','secret','mch_id','pay_sign_key','apiclient_cert','apiclient_key'],
		'update'=>['app_id','secret','mch_id','pay_sign_key','apiclient_cert','apiclient_key'],
	];



}

