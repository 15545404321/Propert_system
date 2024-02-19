<?php 
/*
 module:		应收管理控制器
 create_time:	2023-01-10 09:13:59
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Scys extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'scys_id';

 	protected $name = 'scys';


	function fydy(){
		return $this->hasOne(\app\shop\model\Fydy::class,'fydy_id','fydy_id');
	}

	function louyu(){
		return $this->hasOne(\app\shop\model\Louyu::class,'louyu_id','louyu_id');
	}

	function jflx(){
		return $this->hasOne(\app\shop\model\Jflx::class,'jflx_id','jflx_id');
	}



}

