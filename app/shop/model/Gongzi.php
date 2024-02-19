<?php 
/*
 module:		工资记录控制器
 create_time:	2023-01-18 14:31:51
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Gongzi extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'gongzi_id';

 	protected $name = 'gongzi';


	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}

	function shop(){
		return $this->hasOne(\app\shop\model\Shop::class,'shop_id','shop_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}



}

