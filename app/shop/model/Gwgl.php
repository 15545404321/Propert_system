<?php 
/*
 module:		岗位管理控制器
 create_time:	2023-10-13 16:33:40
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Gwgl extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'gwgl_id';

 	protected $name = 'gwgl';


	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}



}

