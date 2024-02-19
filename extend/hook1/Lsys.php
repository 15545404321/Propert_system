<?php

namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;

use support\Log;

use think\facade\Db;



class Lsys
{

	function afterShopAdd($data) {
/*
 [
  "lsys_kstime" => 1674576000
  "lsys_jstime" => 1674576000
  "jflx_id" => 2
  "fydy_id" => 20
  "fybz_id" => 21
  "fcxx_idx" => "3008,3009,3010,3011,3012,3013,3014,3015,3016,3017,3018,3019,7309,7310,7311,7312,7313,7314,7315,7316,7317,7318,7319,7320,7321,7322,7323,7324,7373,7374,7375,7376,7377,7378,7379,7380,7381,7382,7383,7384,7385,7386,7387,7388,7389,7390,7391,7392,7393,7394,7395,7396,7397,7398,7399,7400,7401,7402,7403,7404,7561,7562,7563,7564,7582,3056,3057,3058,3059,3060,3061,3062,3063,3064,3065,3066,3067,3068,3069,3070,3071,3072,3073,3074,3075,3076"
  "lsys_ysje" => 10
  "lsys_bz" => ""
  "shop_id" => 1
  "xqgl_id" => 9
]
 * */
        $fybz = Db::name('fybz')->where('fybz_id',$data['fybz_id'])->find();
        $fylx_id = Db::name('fydy')->where('fydy_id',$data['fydy_id'])->value('fylx_id');
        $fydy_name = Db::name('fydy')->where('fydy_id',$data['fydy_id'])->value('fydy_name');

        $fcxx_id_arr = explode(',',$data['fcxx_idx']);

        $yysj = [];

        foreach ($fcxx_id_arr as $fcxx_id_arr_item) {

            $fcxx_id    = $fcxx_id_arr_item;
            $yssj_fymc  = $fybz['fybz_name'];

            if ($fylx_id == 3) { // 预收款
                $fcxx_id = 0;
                $yssj_fymc = $fydy_name;
            }

            $member_id = Db::name('fcxx')->where('fcxx_id',$fcxx_id)->value('member_id');

            if ($member_id == 0) {
                continue;
            }

            $yysj[] = [
                'yssj_fymc'     => $yssj_fymc,
                'yssj_cwyf'     => date('Y-m',$data['lsys_jstime']),
                'yssj_kstime'   => $data['lsys_kstime'],
                'yssj_jztime'   => $data['lsys_jstime'],
                'yssj_fydj'     => $fybz['fybz_bzdj'],
                'yssj_ysje'     => $data['lsys_ysje'],
                'fylx_id'       => $fylx_id,
                'fybz_id'       => $data['fybz_id'],
                'sjlx_id'       => 1,
                'yssj_stuats'   => 0,
                'yssj_fksj'     => 0,
                'shop_id'       => session('shop.shop_id'),
                'xqgl_id'       => session('shop.xqgl_id'),
                'fcxx_id'       => $fcxx_id,
                'member_id'     => $member_id,
                'lsys_id'       => $data['lsys_id'],
            ];
        }

        $yssjM = new Yssj();
        $yssjM->saveAll($yysj);

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

    function beforShopUpdate($data) {

        $fcxx_idx = explode(',',$data['fcxx_idx']);

        $yssj = Db::name('yssj')
            ->where('lsys_id',$data['lsys_id'])
            ->where('yssj_stuats',1)
            ->where('fcxx_id','in',$fcxx_idx)
            ->select()->toArray();

        if (!empty($yssj)) {
            return json(['status'=>201,'msg'=>'已有缴费记录不可重新生成']);
        }
    }

    function afterShopUpdate($data) {

        Db::name('yssj')->where('lsys_id',$data['lsys_id'])->delete();
	    return $this->afterShopAdd($data);
    }

    function beforShopDelete($data) {

        $yssj = Db::name('yssj')
            ->where('lsys_id',$data)
            ->where('yssj_stuats',1)
            ->select()->toArray();

        if (!empty($yssj)) {
            return json(['status'=>201,'msg'=>'已有缴费记录不可删除']);
        }

    }

    function afterShopDelete($data) {

        Db::name('yssj')->where('lsys_id',$data)->delete();

        return json(['status'=>200,'msg'=>'操作成功']);

    }
}