<?php 
/*
 module:		车辆管理控制器
 create_time:	2023-03-22 13:52:46
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Car extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'car_id';

 	protected $name = 'car';


	function shop(){
		return $this->hasOne(\app\shop\model\Shop::class,'shop_id','shop_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}

	function member(){
		return $this->hasOne(\app\shop\model\Member::class,'member_id','member_id');
	}



}

