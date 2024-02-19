<?php 
/*
 module:		员工信息控制器
 create_time:	2023-01-03 21:29:39
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Ryxx extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'ryxx_id';

 	protected $name = 'ryxx';


	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}



}

