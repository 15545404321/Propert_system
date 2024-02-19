<?php 
/*
 module:		收银台控制器
 create_time:	2022-12-31 10:06:14
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Syt extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'syt_id';

 	protected $name = 'syt';


	function zkgl(){
		return $this->hasOne(\app\shop\model\Zkgl::class,'zkgl_id','zkgl_id');
	}

	function fcxx(){
		return $this->hasOne(\app\shop\model\Fcxx::class,'fcxx_id','fcxx_id');
	}
}

