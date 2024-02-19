<?php 
/*
 module:		车位信息控制器
 create_time:	2023-03-13 11:10:39
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Cewei extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'cewei_id';

 	protected $name = 'cewei';


	function cwlx(){
		return $this->hasOne(\app\shop\model\Cwlx::class,'cwlx_id','cwlx_id');
	}

	function tccd(){
		return $this->hasOne(\app\shop\model\Tccd::class,'tccd_id','tccd_id');
	}

	function cwqy(){
		return $this->hasOne(\app\shop\model\Cwqy::class,'cwqy_id','cwqy_id');
	}

	function cwzt(){
		return $this->hasOne(\app\shop\model\Cwzt::class,'cwzt_id','cwzt_id');
	}



}

