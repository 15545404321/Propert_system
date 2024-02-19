<?php 
/*
 module:		临时应收控制器
 create_time:	2023-01-10 12:45:34
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Lsys extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'lsys_id';

 	protected $name = 'lsys';


	function jflx(){
		return $this->hasOne(\app\shop\model\Jflx::class,'jflx_id','jflx_id');
	}

	function fybz(){
		return $this->hasOne(\app\shop\model\Fybz::class,'fybz_id','fybz_id');
	}

	function fydy(){
		return $this->hasOne(\app\shop\model\Fydy::class,'fydy_id','fydy_id');
	}



}

