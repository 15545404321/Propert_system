<?php 
/*
 module:		房产信息控制器
 create_time:	2023-01-27 08:18:21
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Fcxx extends validate {


	protected $rule = [
		'louyu_id'=>['require'],
		'fcxx_szlc'=>['require','regex'=>'/^[0-9]*$/'],
		'fcxx_fjbh'=>['require'],
		'fwlx_id'=>['require'],
		'fcxx_ghjl'=>['require'],
	];

	protected $message = [
		'louyu_id.require'=>'楼宇/单元不能为空',
		'fcxx_szlc.require'=>'所在楼层不能为空',
		'fcxx_szlc.regex'=>'所在楼层格式错误',
		'fcxx_fjbh.require'=>'房间编号不能为空',
		'fwlx_id.require'=>'房屋类型不能为空',
		'fcxx_ghjl.require'=>'是否过户不能为空',
	];

	protected $scene  = [
		'add'=>['louyu_id','fcxx_szlc','fcxx_fjbh','fwlx_id'],
		'update'=>['fwlx_id'],
		'zcgl'=>[''],
		'batupdate'=>[''],
		'fwlxupdate'=>['fwlx_id'],
	];



}

