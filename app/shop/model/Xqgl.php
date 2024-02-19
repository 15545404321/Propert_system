<?php 
/*
 module:		项目管理控制器
 create_time:	2023-01-08 10:57:53
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Xqgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'xqgl_id';

 	protected $name = 'xqgl';


	function shop(){
		return $this->hasOne(\app\shop\model\Shop::class,'shop_id','shop_id');
	}



}

