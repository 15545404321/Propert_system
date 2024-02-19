<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;


class Fcxx

{
//过户记录
    function afterShopIndex($data) {

    /*dump($data);
      exit;

      $data['fcxx_ghjl']=0;
      return json(['status'=>200,'msg'=>'修改成功']);*/

    }

    function beforShopDelete($data) {

        $data = explode(',',$data);
        $count = Db::name('yssj')->where('fcxx_id','in',$data)->count();
        if (!empty($count)) {
            return json(['status'=>201,'msg'=>'该房间有费用发生，不可删除']);
        }
        
    }

    function beforShopZcgl($data) {

        $data = explode(',',$data);
        $count = Db::name('yssj')->where('fcxx_id','in',$data)->count();
        if (!empty($count)) {
            return json(['status'=>201,'msg'=>'该房间有费用发生，不可直接修改资产关联']);
        }

    }

}