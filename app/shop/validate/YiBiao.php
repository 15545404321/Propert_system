<?php 
/*
 module:		仪表管理控制器
 create_time:	2023-01-08 11:22:55
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class YiBiao extends validate {


	protected $rule = [
//		'yibiao_sn'=>['unique:yibiao'],
		'yblx_id'=>['require'],
		'ybzl_id'=>['require'],
		'yibiao_ybbl'=>['require','regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
		'yibiao_csds'=>['require','regex'=>'/^[0-9]*$/'],
		'yibiao_yblc'=>['require','regex'=>'/^[0-9]*$/'],
		'add_time'=>['require'],
		'yibiao_status'=>['require'],
	];

	protected $message = [
//		'yibiao_sn.unique'=>'仪表编号已经存在',
		'yblx_id.require'=>'仪表类型不能为空',
		'ybzl_id.require'=>'仪表种类不能为空',
		'yibiao_ybbl.require'=>'仪表倍率不能为空',
		'yibiao_ybbl.regex'=>'仪表倍率格式错误',
		'yibiao_csds.require'=>'初始底数不能为空',
		'yibiao_csds.regex'=>'初始底数格式错误',
		'yibiao_yblc.require'=>'仪表量程不能为空',
		'yibiao_yblc.regex'=>'仪表量程格式错误',
		'add_time.require'=>'安装时间不能为空',
		'yibiao_status.require'=>'仪表状态不能为空',
	];

	protected $scene  = [
		'add'=>['yibiao_sn','yblx_id','ybzl_id','yibiao_ybbl','yibiao_csds','yibiao_yblc','add_time','yibiao_status'],
		'update'=>['yibiao_sn','yblx_id','ybzl_id','yibiao_ybbl','yibiao_csds','yibiao_yblc','add_time','yibiao_status'],
	];



}

