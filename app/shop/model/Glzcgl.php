<?php 
/*
 module:		关联资产管理控制器
 create_time:	2022-12-26 08:38:38
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Glzcgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'glzcgl_id';

 	protected $name = 'glzcgl';


	function zclx(){
		return $this->hasOne(\app\shop\model\Zclx::class,'zclx_id','zclx_id');
	}

	function shopadmin(){
		return $this->hasOne(\app\shop\model\ShopAdmin::class,'shop_admin_id','shop_admin_id');
	}

	function khlx(){
		return $this->hasOne(\app\shop\model\Khlx::class,'khlx_id','khlx_id');
	}

	function louyu(){
		return $this->hasOne(\app\shop\model\Louyu::class,'louyu_id','louyu_id');
	}

	function fcxx(){
		return $this->hasOne(\app\shop\model\Fcxx::class,'fcxx_id','fcxx_id');
	}

	function tccd(){
		return $this->hasOne(\app\shop\model\Tccd::class,'tccd_id','tccd_id');
	}

	function cewei(){
		return $this->hasOne(\app\shop\model\Cewei::class,'cewei_id','cewei_id');
	}



}

