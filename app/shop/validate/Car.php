<?php 
/*
 module:		车辆管理控制器
 create_time:	2023-03-22 13:52:46
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Car extends validate {


	protected $rule = [
		'car_name'=>['require'],
	];

	protected $message = [
		'car_name.require'=>'车牌号不能为空',
	];

	protected $scene  = [
		'add'=>['car_name'],
		'update'=>['car_name'],
		'zcgl'=>[''],
	];



}

