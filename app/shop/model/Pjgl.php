<?php 
/*
 module:		票据管理控制器
 create_time:	2023-01-18 11:56:05
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Pjgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'pjgl_id';

 	protected $name = 'pjgl';


	function pjlx(){
		return $this->hasOne(\app\shop\model\Pjlx::class,'pjlx_id','pjlx_id');
	}

	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}



}

