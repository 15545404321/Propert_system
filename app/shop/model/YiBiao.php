<?php 
/*
 module:		仪表管理控制器
 create_time:	2023-01-08 11:22:55
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class YiBiao extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'yibiao_id';

 	protected $name = 'yibiao';


	function shop(){
		return $this->hasOne(\app\shop\model\Shop::class,'shop_id','shop_id');
	}

	function xqgl(){
		return $this->hasOne(\app\shop\model\Xqgl::class,'xqgl_id','xqgl_id');
	}

	function yblx(){
		return $this->hasOne(\app\shop\model\Yblx::class,'yblx_id','yblx_id');
	}

	function ybzl(){
		return $this->hasOne(\app\shop\model\Ybzl::class,'ybzl_id','ybzl_id');
	}

	function louyu(){
		return $this->hasOne(\app\shop\model\Louyu::class,'louyu_id','louyu_id');
	}

    function fcxx(){
        return $this->hasOne(\app\shop\model\Fcxx::class,'fcxx_id','fcxx_id');
    }


}

