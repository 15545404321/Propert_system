<?php 
/*
 module:		应收数据控制器
 create_time:	2023-01-28 13:14:36
 author:		
 contact:		
*/

namespace app\shop\model;
use think\Model;

class Yssj extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'yssj_id';

 	protected $name = 'yssj';


 /*start*/
    function fylx(){
        return $this->hasOne(\app\shop\model\Fylx::class,'fylx_id','fylx_id');
    }

    function fybz(){
        return $this->hasOne(\app\shop\model\Fybz::class,'fybz_id','fybz_id');
    }

    function fcxx(){
        return $this->hasOne(\app\shop\model\Fcxx::class,'fcxx_id','fcxx_id');
    }

    function member(){
        return $this->hasOne(\app\shop\model\Member::class,'member_id','member_id');
    }


    function cewei(){
        return $this->hasOne(\app\shop\model\Cewei::class,'cewei_id','cewei_id');
    }

    function louyu(){
        return $this->hasOne(\app\shop\model\Louyu::class,'louyu_id','louyu_id');
    }
    /*end*/



}

