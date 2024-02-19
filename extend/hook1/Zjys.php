<?php
namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;
use support\Log;
use think\facade\Db;
use app\shop\model\Cbgl;

class Zjys
{

    function afterShopAdd($data) {

        $fybz = Db::name('fybz')->where('fybz_id',$data['fybz_id'])->find();
        $fylx_id = Db::name('fydy')->where('fydy_id',$data['fydy_id'])->value('fylx_id');
        $fydy_name = Db::name('fydy')->where('fydy_id',$data['fydy_id'])->value('fydy_name');


        $fcxx_id    = $data['fcxx_id'];
        $yssj_fymc  = $fybz['fybz_name'];

        if ($fylx_id == 3) { // 预收款
//            $fcxx_id = $fcxx_id;
            $yssj_fymc = $fydy_name;
        }

        // 更新追加应收 费用类型
        Db::name('zjys')->where('zjys_id',$data['zjys_id'])->update(['fylx_id'=>$fylx_id]);

        $yysj = [
            'yssj_fymc'     => $yssj_fymc,
            'fydy_id'       => $data['fydy_id'],
            'yssj_cwyf'     => date('Y-m',$data['zjys_jtime']),
            'yssj_kstime'   => $data['zjys_ktime'],
            'yssj_jztime'   => $data['zjys_jtime'],
            'yssj_fydj'     => $data['zjys_dcys'],
            'yssj_ysje'     => $data['zjys_bcys'],
            'fylx_id'       => $fylx_id,
            'fybz_id'       => $data['fybz_id'],
            'sjlx_id'       => 1,
            'yssj_stuats'   => 0,
            'yssj_fksj'     => 0,
            'shop_id'       => session('shop.shop_id'),
            'xqgl_id'       => session('shop.xqgl_id'),
            'fcxx_id'       => $fcxx_id,
            'member_id'     => $data['member_id'],
            'zjys_id'       => $data['zjys_id'],
        ];

        $res = Db::name('yssj')->insert($yysj);

        $member_id_column[$data['member_id']] = $data['member_id'];
        
        $member_yssj_ysje = Db::name('yssj')
            ->where('member_id','in',$member_id_column)
            ->where('yssj_stuats',0)
            ->group('member_id')
            ->column('sum(yssj_ysje)','member_id');

        $member_yingshou = [];
        foreach ($member_yssj_ysje as $member_yssj_ysje_key => $member_yssj_ysje_item) {
            $member_yingshou[] = [
                'member_id' => $member_yssj_ysje_key,
                'member_yingshou' => $member_yssj_ysje_item
            ];
        }

        $memberM = new \app\shop\model\Member();
        $memberM->saveAll($member_yingshou);

        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
    }


    function beforShopDelete($data) {

        $yssj_cwsh = Db::name('yssj')->where('zjys_id',$data)->value('yssj_cwsh');

        if ($yssj_cwsh == 2) {
            return json(['status'=>201,'msg'=>'财务已确认，不可删除']);
        }

        $yssj = Db::name('yssj')->where('zjys_id',$data)->find();
        $member = Db::name('member')->where('member_id',$yssj['member_id'])->find();

        if ($yssj['yssj_ysje'] > $member['member_yucun']) {
            return json(['status'=>201,'msg'=>'用户预收款余额不足退款']);
        }

        if ($yssj['fylx_id'] == 3) { // 预收款
            Db::name('member')->where('member_id',$yssj['member_id'])
                ->dec('member_yucun', $yssj['yssj_ysje'])->update();
        }

    }


    function afterShopDelete($data) {

//        $yssj = Db::name('yssj')->where('zjys_id',$data)->find();

//        Db::name('yssj')->where('zjys_id',$data)->delete();

        /*if ($yssj['fylx_id'] == 3) { // 预收款

            Db::name('member')->where('member_id',$yssj['member_id'])
                ->dec('member_yucun', $yssj['yssj_ysje'])->update();
        }*/

        return json(['status'=>200,'msg'=>'操作成功']);
    }

}

