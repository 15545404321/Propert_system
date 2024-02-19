<?php 
/*
 module:		客户信息控制器
 create_time:	2023-01-13 08:04:18
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Member extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'member_id';

 	protected $name = 'member';


	function zjlx(){
		return $this->hasOne(\app\shop\model\Zjlx::class,'zjlx_id','zjlx_id');
	}

	function shop(){
		return $this->hasOne(\app\shop\model\Shop::class,'shop_id','shop_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}



}

