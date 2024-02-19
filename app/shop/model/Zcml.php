<?php 
/*
 module:		资产台账控制器
 create_time:	2023-01-06 16:02:34
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Zcml extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'zcml_id';

 	protected $name = 'zcml';


	function zclb(){
		return $this->hasOne(\app\shop\model\Zclb::class,'zclb_id','zclb_fid');
	}



}

