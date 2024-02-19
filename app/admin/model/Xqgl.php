<?php 
/*
 module:		项目管理控制器
 create_time:	2023-02-06 12:06:09
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Xqgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'xqgl_id';

 	protected $name = 'xqgl';


	function shop(){
		return $this->hasOne(\app\admin\model\Shop::class,'shop_id','shop_id');
	}



}

