<?php 
/*
 module:		楼宇信息控制器
 create_time:	2023-01-09 15:05:06
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Louyu extends validate {


	protected $rule = [
		'louyu_name'=>['require'],
		'louyutype_id'=>['require'],
		'louyusx_id'=>['require'],
		'louyu_dysl'=>['require','regex'=>'/^[0-9]*$/'],
		'louyu_lczs'=>['require','regex'=>'/^[0-9]*$/'],
		'louyu_chzs'=>['require','regex'=>'/^[0-9]*$/'],
		'louyu_flcs'=>['require','regex'=>'/^[0-9]*$/'],
		'louyu_jzmj'=>['regex'=>'/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/'],
		'louyu_dtsl'=>['regex'=>'/^[0-9]*$/'],
		'louyu_dscs'=>['require','regex'=>'/^[0-9]*$/'],
		'louyu_ycjh'=>['require','regex'=>'/^[0-9]*$/'],
	];

	protected $message = [
		'louyu_name.require'=>'楼宇名称不能为空',
		'louyutype_id.require'=>'楼宇类型不能为空',
		'louyusx_id.require'=>'楼房属性不能为空',
		'louyu_dysl.require'=>'单元数量不能为空',
		'louyu_dysl.regex'=>'单元数量格式错误',
		'louyu_lczs.require'=>'楼层总数不能为空',
		'louyu_lczs.regex'=>'楼层总数格式错误',
		'louyu_chzs.require'=>'层户总数不能为空',
		'louyu_chzs.regex'=>'层户总数格式错误',
		'louyu_flcs.require'=>'负楼层数不能为空',
		'louyu_flcs.regex'=>'负楼层数格式错误',
		'louyu_jzmj.regex'=>'建筑面积格式错误',
		'louyu_dtsl.regex'=>'电梯数量格式错误',
		'louyu_dscs.require'=>'底商层数不能为空',
		'louyu_dscs.regex'=>'底商层数格式错误',
		'louyu_ycjh.require'=>'一层几户不能为空',
		'louyu_ycjh.regex'=>'一层几户格式错误',
	];

	protected $scene  = [
		'add'=>['louyu_name','louyutype_id','louyusx_id','louyu_dysl','louyu_lczs','louyu_chzs','louyu_flcs','louyu_jzmj','louyu_dtsl','louyu_dscs','louyu_ycjh'],
		'update'=>['louyu_name','louyutype_id','louyusx_id','louyu_jzmj','louyu_dtsl'],
	];



}

