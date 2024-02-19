<?php 
/*
 module:		结算批次控制器
 create_time:	2023-01-13 13:21:57
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Xzpici extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'xzpici_id';

 	protected $name = 'xzpici';


	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}



}

