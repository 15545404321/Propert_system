<?php 
/*
 module:		分配房间控制器
 create_time:	2023-01-05 11:28:38
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Fyfp extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'fyfp_id';

 	protected $name = 'fyfp';


	function fcxx(){
		return $this->hasOne(\app\shop\model\Fcxx::class,'fcxx_id','fcxx_id');
	}

	function fybz(){
		return $this->hasOne(\app\shop\model\Fybz::class,'fybz_id','fybz_id');
	}

	function fydy(){
		return $this->hasOne(\app\shop\model\Fydy::class,'fydy_id','fydy_id');
	}

	function louyu(){
		return $this->hasOne(\app\shop\model\Louyu::class,'louyu_id','louyu_id');
	}

	function fwlx(){
		return $this->hasOne(\app\shop\model\Fwlx::class,'fwlx_id','fwlx_id');
	}



}

