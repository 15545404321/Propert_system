<?php 
/*
 module:		折扣管理控制器
 create_time:	2022-12-19 00:06:14
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Zkgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'zkgl_id';

 	protected $name = 'zkgl';


	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}



}

