<?php
namespace hook;
use think\exception\ValidateException;
use support\Log;
use think\facade\Db;

class Fydy
{

    function beforShopDelete($data){

        $fydy_id = explode(',',$data);

        $yssj = Db::name('fybz')->alias('a')
            ->leftJoin('yssj b','a.fybz_id=b.fybz_id')
            ->where('a.fydy_id','in',$fydy_id)->select()->toArray();

        if (!empty($yssj)) {
            return json(['status'=>201,'msg'=>'有缴费记录不可删除']);
        }
    }


    function afterShopDelete($data){
        $fydy_id = explode(',',$data);
        Db::name('fybz')->where('fydy_id','in',$fydy_id)->delete();
    }
}