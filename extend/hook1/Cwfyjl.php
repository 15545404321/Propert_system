<?php
namespace hook;

use think\exception\ValidateException;
use support\Log;
use think\facade\Db;
use app\shop\model\Cbgl;
use app\shop\model\Yssj as YssjM;

class Cwfyjl
{
    function beforShopDelete($data) {

        if (empty($data)) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }

        $yssj_ids = explode(',',$data);

        $yssj = Db::name('yssj')
            ->where('yssj_id','in',$yssj_ids)->select();

        foreach ($yssj as $yssj_item) {
            if ($yssj_item['yssj_stuats'] != 0) {

                return json(['status'=>201,'msg'=>'已收款记录不能删除']);
            }
        }

    }

    function afterShopDelete($data) {

        if (empty($data)) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }

        $yssj_ids = explode(',',$data);

        $yssj = Db::name('yssj')
            ->where('yssj_id','in',$yssj_ids)->select();

        $member_id_column = [];
        foreach ($yssj as $yssj_item) {
            $member_id_column[$yssj_item['member_id']] = $yssj_item['member_id'];
        }

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

    }

    function beforShopCxsc($data) {

        if (empty($data)) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }

        $yssj_ids = explode(',',$data);

        $yssj = Db::name('yssj')
            ->where('yssj_id','in',$yssj_ids)->select();

        foreach ($yssj as $yssj_item) {
            if ($yssj_item['yssj_stuats'] == 0) {

                return json(['status'=>201,'msg'=>'已收款记录不能重新生成']);
            }
        }

        exit;

        $yssj_id_arr = explode(',',$data);

        $fybz_jfgs = Db::name('yssj')->alias('a')
            ->leftJoin('fybz b','a.fybz_id=b.fybz_id')
            ->where('yssj_id','in',$yssj_id_arr)->column('b.jfgs_id','a.fybz_id');

        $yssj = Db::name('yssj')->alias('a')
            ->field('a.*,b.fcxx_jzmj,c.fybz_bzdj')
            ->leftJoin('fcxx b', 'a.fcxx_id=b.fcxx_id')
            ->leftJoin('fybz c', 'a.fybz_id=c.fybz_id')
            ->where('yssj_id','in',$yssj_id_arr)->select();

        $yssj_data = [];
        $member_id_arr = [];
        foreach ($yssj as $yssj_item) {
            $yssj_ysje = $yssj_item['yssj_ysje'];
            foreach ($fybz_jfgs as $fybz_jfgs_key => $fybz_jfgs_item) {

                if ($fybz_jfgs_key == $yssj_item['fybz_id']) {

                    if ($fybz_jfgs_item == 1) { // 1)单价
                        $yssj_ysje = $yssj_item['fybz_bzdj'];
                    } elseif ($fybz_jfgs_item == 2) { // 2)单价*使用面积
                        $yssj_ysje = $yssj_item['fybz_bzdj']*$yssj_item['fcxx_jzmj'];
                    }
                    continue;
                }

            }

            $yssj_data[] = [
                'yssj_id'       => $yssj_item['yssj_id'],
                'yssj_fymc'     => $yssj_item['yssj_fymc'],
                'yssj_cwyf'     => $yssj_item['yssj_cwyf'],
                'yssj_kstime'   => $yssj_item['yssj_kstime'],
                'yssj_jztime'   => $yssj_item['yssj_jztime'],
                'yssj_fydj'     => $yssj_item['yssj_fydj'],
                'yssj_ysje'     => $yssj_ysje,
                'fylx_id'       => $yssj_item['fylx_id'],
                'fybz_id'       => $yssj_item['fybz_id'],
                'yssj_stuats'   => $yssj_item['yssj_stuats'],
                'yssj_fksj'     => $yssj_item['yssj_fksj'],
                'cbgl_id'       => $yssj_item['cbgl_id'],
                'shop_id'       => $yssj_item['shop_id'],
                'xqgl_id'       => $yssj_item['xqgl_id'],
                'fcxx_id'       => $yssj_item['fcxx_id'],
                'member_id'     => $yssj_item['member_id'],
                'syt_id'        => $yssj_item['syt_id'],
                'cewei_id'      => $yssj_item['cewei_id'],
                'scys_id'       => $yssj_item['scys_id'],
                'sjlx_id'       => $yssj_item['sjlx_id'],
                'zjys_id'       => $yssj_item['zjys_id'],
                'lsys_id'       => $yssj_item['lsys_id'],
                'cbpc_id'       => $yssj_item['cbpc_id'],
                'yssj_cwsh'     => $yssj_item['yssj_cwsh']
            ];
            $member_id_arr[] = $yssj_item['member_id'];
        }

        $yssjM = new YssjM();
        $yssjM->saveAll($yssj_data);


        $member_yssj_ysje = Db::name('yssj')
            ->where('member_id','in',$member_id_arr)
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

    }

}

/*
dump($yssj['cbgl_id']);
dump($yssj);

$yssj_stuats = Db::name('yssj')->where('zjys_id',$data)->value('yssj_stuats');

if ($yssj_stuats == 1) {
    return json(['status'=>201,'msg'=>'已有缴费记录不可删除']);
}
*/
