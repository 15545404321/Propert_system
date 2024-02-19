<?php 
/*
 module:		领用记录控制器
 create_time:	2023-01-06 16:02:08
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Ysly extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'ysly_id';

 	protected $name = 'ysly';


	function ysfl(){
		return $this->hasOne(\app\shop\model\Ysfl::class,'ysfl_id','ysfl_id');
	}



}

