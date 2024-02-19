<?php 
/*
 module:		计费方式列表控制器
 create_time:	2023-01-10 09:42:12
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Jffslb extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'fybz_id';

 	protected $name = 'fybz';


	function jfgs(){
		return $this->hasOne(\app\shop\model\Jfgs::class,'jfgs_id','jfgs_id');
	}

	function fydy(){
		return $this->hasOne(\app\shop\model\Fydy::class,'fydy_id','fydy_id');
	}

	function fylx(){
		return $this->hasOne(\app\shop\model\Fylx::class,'fylx_id','fylx_id');
	}



}

