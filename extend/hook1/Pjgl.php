<?php

namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

//use \app\shop\model\Cbgl as CbglM;



class Pjgl

{
    function afterShopAdd($data) {

        if ($data['pjgl_status'] == 1) {
            Db::name('pjgl')
                ->where('pjlx_id',$data['pjlx_id'])
                ->where('xqgl_id',$data['xqgl_id'])
                ->where('pjgl_id','<>',$data['pjgl_id'])
                ->update(['pjgl_status' => 0]);
        }

    }

    function afterShopUpdate($data) {

        if ($data['pjgl_status'] == 1) {
            Db::name('pjgl')
                ->where('pjlx_id',$data['pjlx_id'])
                ->where('xqgl_id',$data['xqgl_id'])
                ->where('pjgl_id','<>',$data['pjgl_id'])
                ->update(['pjgl_status' => 0]);
        }

    }

    function beforShopDelete($data) {

        $id_arr = explode(',',$data);
        foreach ($id_arr as $id_arr_item) {

            $pjgl_status = Db::name('pjgl')->where('pjgl_id',$id_arr_item)->value('pjgl_status');
            if ($pjgl_status == 1) {
                return json(['status'=>201,'data'=>$data,'msg'=>'该票据是启用状态，不可删除！']);
            }
        }

    }
}