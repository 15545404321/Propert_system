<?php 
/*
 module:		费用标准控制器
 create_time:	2023-01-17 11:14:32
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Fybz extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'fybz_id';

 	protected $name = 'fybz';


	function jfgs(){
		return $this->hasOne(\app\shop\model\Jfgs::class,'jfgs_id','jfgs_id');
	}



}

