<?php

namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

//use \app\shop\model\Cbgl as CbglM;



class Cbgl
{

    function afterShopBatupdate($data) {

        if (empty($data['cbgl_id']) || is_array($data['cbgl_id'])) {

            return json(['status'=>201,'msg'=>'参数错误']);

        }

        $cbgl = Db::name('cbgl')->where('cbgl_id',$data['cbgl_id'])->find();

        $cbgl_sqsl = $cbgl['cbgl_sqsl']; // 上期数量

        $cbgl_bqsl = $cbgl['cbgl_bqsl']; // 本期数量

        $cbgl_shyl = $cbgl['cbgl_shyl']; // 损耗数量

        $cbgl_cbyl = $cbgl_bqsl - $cbgl_sqsl; // 抄表用量

        $cbgl_sjyl = $cbgl_bqsl - $cbgl_sqsl - $cbgl_shyl; // 实际用量

        $qzfs = Db::name('fybz')->alias('a')
            ->field('c.*')
            ->leftJoin('fydy b','a.fydy_id=b.fydy_id')
            ->leftJoin('qzfs c','b.qzfs_id=c.qzfs_id')
            ->where('fybz_id',$cbgl['fybz_id'])
            ->find();

        $cbgl_cbje = $cbgl_sjyl * $cbgl['cbgl_ybbl'] * $cbgl['fybz_bzdj'];

        if ($qzfs['qzfs_qzws'] == 0) {
            $cbgl_cbje = intval(round($cbgl_cbje));
        } else {
            $cbgl_cbje = round($cbgl_cbje, $qzfs['qzfs_qzws']);
        }

        $cbgl_data = [

            'cbgl_cbyl'     => $cbgl_cbyl, // 本期数量 - 上期数量

            'cbgl_sjyl'     => $cbgl_sjyl, // 本期数量 - 上期数量 - 损耗用量

            'cbgl_cbje'     => $cbgl_cbje,

        ];

        Db::name('cbgl')->where('cbgl_id',$data['cbgl_id'])->update($cbgl_data);



        return json(['status'=>200,'msg'=>'修改成功']);

    }

    function beforShopDanHuRuZhang($data) {

        if (empty($data)) { return json(['status'=>201,'msg'=>'参数错误']); }

        $cbgl = Db::name('cbgl')->where('cbgl_id',$data)->find();

        if ($cbgl['cbgl_bqsl'] == 0) {
            return json(['status'=>201,'msg'=>'未抄入本期数量']);
        }

        if ($cbgl['cbgl_status'] == 1) {
            return json(['status'=>201,'msg'=>'该抄表已入账']);
        }

    }

    function afterShopDanHuRuZhang($data) {

        if (empty($data)) { return json(['status'=>201,'msg'=>'参数错误']); }

        Db::name('cbgl')->where('cbgl_id',$data)->update(['cbgl_status' => 1]);

        $cbgl = Db::name('cbgl')->where('cbgl_id',$data)->find();

        $fydy_id = Db::name('fybz')->where('fybz_id',$cbgl['fybz_id'])->value('fydy_id');

        $yssj_data[] = [
            'yssj_fymc'     => $cbgl['fybz_name'],
            'fydy_id'       => $fydy_id,
            'yssj_cwyf'     => $cbgl['cbgl_cwyf'],
            'yssj_kstime'   => $cbgl['cbgl_kstime'],
            'yssj_jztime'   => $cbgl['cbgl_jstime'],
            'yssj_fydj'     => $cbgl['fybz_bzdj'],
            'yssj_ysje'     => $cbgl['cbgl_cbje'],
            'fylx_id'       => 2,
            'fybz_id'       => $cbgl['fybz_id'],
            'cbgl_id'       => $cbgl['cbgl_id'],
            'shop_id'       => session('shop.shop_id'),
            'xqgl_id'       => session('shop.xqgl_id'),
            'fcxx_id'       => $cbgl['fcxx_id'],
            'yssj_stuats'   => 0,
            'yssj_fksj'     => '',
            'sjlx_id'       => 1,
            'member_id'     => $cbgl['member_id'],
            'syt_id'        => null,
        ];
        $member_id_column[$cbgl['member_id']] = $cbgl['member_id'];

        $yssj = new Yssj();
        $yssj->saveAll($yssj_data);

        $cbgl_de = Db::name('cbgl')
            ->where('cbpc_id',$cbgl['cbpc_id'])->where('cbgl_status',0)
            ->select()->toArray();

        if (empty($cbgl_de)) {
            Db::name('cbpc')->where('cbpc_id',$cbgl['cbpc_id'])
                ->update(['cbpc_status' => 1]);
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

        return json(['status'=>200,'msg'=>'操作成功']);
    }

    function beforShopPiLiangRuZhang($data) {

        if (empty($data)) { return json(['status'=>201,'msg'=>'参数错误']); }

        $where = [];
        $where[] = ['cbgl_id','in',explode(',',$data)];


        $cbgl = Db::name('cbgl')->where($where)->select();

        foreach ($cbgl as $cbgl_item) {

            if (  $cbgl_item['cbgl_sqsl'] != 0 && $cbgl_item['cbgl_bqsl'] == 0) {
                return json(['status'=>201,'msg'=>'有本期数量未抄入']);
            }

            if ($cbgl_item['cbgl_status'] == 1) {
                return json(['status'=>201,'msg'=>'有抄表已入账']);
            }
        }

    }

    function afterShopPiLiangRuZhang($data) {

        if (empty($data)) { return json(['status'=>201,'msg'=>'参数错误']); }

        $where = [];
        $where[] = ['cbgl_id','in',explode(',',$data)];

        Db::name('cbgl')->where($where)->update(['cbgl_status' => 1]);

        $cbgl = Db::name('cbgl')->where($where)->select();

        $fydy_id = Db::name('fybz')->where('fybz_id',$cbgl['fybz_id'])->value('fydy_id');

        $yssj_data = [];
        $member_id_column = [];
        foreach ($cbgl as $cbgl_item) {

            if (  $cbgl_item['cbgl_sqsl'] != 0 && $cbgl_item['cbgl_bqsl'] == 0) {
                return json(['status'=>201,'msg'=>'有本期数量未抄入']);
            }

            $yssj_data[] = [
                'yssj_fymc'     => $cbgl_item['fybz_name'],
                'fydy_id'       => $fydy_id,
                'yssj_cwyf'     => $cbgl_item['cbgl_cwyf'],
                'yssj_kstime'   => $cbgl_item['cbgl_kstime'],
                'yssj_jztime'   => $cbgl_item['cbgl_jstime'],
                'yssj_fydj'     => $cbgl_item['fybz_bzdj'],
                'yssj_ysje'     => $cbgl_item['cbgl_cbje'],
                'fylx_id'       => 2,
                'fybz_id'       => $cbgl_item['fybz_id'],
                'cbgl_id'       => $cbgl_item['cbgl_id'],
                'shop_id'       => session('shop.shop_id'),
                'xqgl_id'       => session('shop.xqgl_id'),
                'fcxx_id'       => $cbgl_item['fcxx_id'],
                'yssj_stuats'   => 0,
                'yssj_fksj'     => '',
                'sjlx_id'       => 1,
                'member_id'     => $cbgl_item['member_id'],
                'syt_id'        => null,
            ];
            $member_id_column[$cbgl_item['member_id']] = $cbgl_item['member_id'];
        }

        $yssj = new Yssj();
        $yssj->saveAll($yssj_data);

        foreach ($cbgl as $cbgl_value) {

            $cbgl_de = Db::name('cbgl')
                ->where('cbpc_id',$cbgl_value['cbpc_id'])->where('cbgl_status',0)
                ->select()->toArray();

            if (empty($cbgl_de)) {
                Db::name('cbpc')->where('cbpc_id',$cbgl_value['cbpc_id'])
                    ->update(['cbpc_status' => 1]);
            }

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

        return json(['status'=>200,'msg'=>'操作成功']);
    }

    public function beforShopCheXiaoRuZhang($data) {

        if (empty($data)) { return json(['status'=>201,'msg'=>'参数错误']); }

        $where = [];
        $where[] = ['cbgl_id','in',explode(',',$data)];

        $yssj = Db::name('yssj')->where($where)->select();

        foreach ($yssj as $yssj_item) {

            if ($yssj_item['yssj_stuats'] == 1) {
                return json(['status'=>201,'msg'=>'勾选项包含已缴费的入账抄表']);
            }

            if ($yssj_item['yssj_stuats'] == 2) {
                return json(['status'=>201,'msg'=>'勾选项包含已退款的入账抄表']);
            }

        }

    }

    public function afterShopCheXiaoRuZhang($data) {

        // 删除费用

        $where = [];
        $where[] = ['cbgl_id','in',explode(',',$data)];

        Db::name('yssj')->where($where)->delete();

        $cbpc_id_arr = Db::name('cbgl')->where($where)->column('cbpc_id');

        Db::name('cbpc')->where('cbpc_id','in',$cbpc_id_arr)->update(['cbpc_status'=>0]);

        $member_id_column = Db::name('cbgl')->where($where)->column('member_id');
        $member_id_column = array_unique($member_id_column);

        $member_yssj_ysje = Db::name('yssj')
            ->where('member_id','in',$member_id_column)
            ->where('yssj_stuats',0)
            ->group('member_id')
            ->column('sum(yssj_ysje)','member_id');

        $member_yingshou = [];
        $member_id_update = [];
        foreach ($member_yssj_ysje as $member_yssj_ysje_key => $member_yssj_ysje_item) {
            $member_yingshou[] = [
                'member_id' => $member_yssj_ysje_key,
                'member_yingshou' => $member_yssj_ysje_item
            ];
            $member_id_update[] = $member_yssj_ysje_key;
        }

        foreach ($member_id_column as $member_id_key => $member_id_item) {
            if (!in_array($member_id_item,$member_id_update)) {
                $member_yingshou[] = [
                    'member_id' => $member_id_item,
                    'member_yingshou' => 0
                ];
            }
        }

        $memberM = new \app\shop\model\Member();
        $memberM->saveAll($member_yingshou);
    }
}
