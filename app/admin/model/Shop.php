<?php 
/*
 module:		物业管理控制器
 create_time:	2023-02-14 09:46:28
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Shop extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'shop_id';

 	protected $name = 'shop';


	function shoprole(){
		return $this->hasOne(\app\admin\model\ShopRole::class,'role_id','goumai');
	}



}

