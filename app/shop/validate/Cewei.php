<?php 
/*
 module:		车位信息控制器
 create_time:	2023-03-13 11:10:39
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Cewei extends validate {


	protected $rule = [
		'cewei_name'=>['require','regex'=>'/^[0-9]*$/'],
		'tccd_id'=>['require'],
		'cwqy_id'=>['require'],
		'cwzt_id'=>['require'],
		'cewei_cwmj'=>['require'],
		'cw_num'=>['require','regex'=>'/^[0-9]*$/'],
		'cwlx_id'=>['require'],
	];

	protected $message = [
		'cewei_name.require'=>'车位编号不能为空',
		'cewei_name.regex'=>'车位编号格式错误',
		'tccd_id.require'=>'停车场地不能为空',
		'cwqy_id.require'=>'车位区域不能为空',
		'cwzt_id.require'=>'车位状态不能为空',
		'cewei_cwmj.require'=>'车位面积不能为空',
		'cw_num.require'=>'新增数量不能为空',
		'cw_num.regex'=>'新增数量格式错误',
		'cwlx_id.require'=>'车位类型不能为空',
	];

	protected $scene  = [
		'add'=>['cewei_name','tccd_id','cwqy_id','cwzt_id','cewei_cwmj','cw_num','cwlx_id'],
		'update'=>['cewei_name','tccd_id','cwqy_id','cwzt_id','cewei_cwmj','cwlx_id'],
		'zcgl'=>[''],
		'sxupdate'=>['tccd_id','cwqy_id','cwzt_id','cewei_cwmj','cwlx_id'],
	];



}

