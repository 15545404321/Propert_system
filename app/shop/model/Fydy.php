<?php 
/*
 module:		费用定义控制器
 create_time:	2023-01-16 14:25:22
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Fydy extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'fydy_id';

 	protected $name = 'fydy';


	function fylx(){
		return $this->hasOne(\app\shop\model\Fylx::class,'fylx_id','fylx_id');
	}

	function fylb(){
		return $this->hasOne(\app\shop\model\Fylb::class,'fylb_id','fylb_id');
	}

	function fydw(){
		return $this->hasOne(\app\shop\model\Fydw::class,'fydw_id','fydw_id');
	}



}

