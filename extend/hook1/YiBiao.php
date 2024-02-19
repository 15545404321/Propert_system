<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class YiBiao
{

    function beforShopAdd($data) {

        $yibiao = Db::name('yibiao')->where('yibiao_sn',$data['yibiao_sn'])
            ->where('xqgl_id',session('shop.xqgl_id'))
            ->find();

        if (!empty($yibiao)) {
            return json(['status'=>201,'msg'=>'仪表编号已经存在']);
        }

        // 是否入住
        $fcxx_member_id = Db::name('fcxx')->where('fcxx_id',$data['fcxx_id'])->value('member_id');
        if (empty($fcxx_member_id)) {
            return json(['status'=>201,'msg'=>'该房间未入住']);
        }

        // 房间是否已分配该种类仪表
        /*$fcxx_ybzl_id = Db::name('fcxx')->alias('a')
            ->leftJoin('yibiao b','a.fcxx_id=b.fcxx_id')
            ->where('a.fcxx_id',$data['fcxx_id'])
            ->where('b.ybzl_id',$data['ybzl_id'])
            ->where('b.yibiao_status',1)
            ->find();
        if (!empty($fcxx_ybzl_id)) {
            return json(['status'=>201,'msg'=>'该房间已绑定该种类仪表']);
        }*/

    }

    function beforShopUpdate($data) {
        // 是否入住
        $fcxx_member_id = Db::name('fcxx')->where('fcxx_id',$data['fcxx_id'])->value('member_id');
        if (empty($fcxx_member_id)) {
            return json(['status'=>201,'msg'=>'该房间未入住']);
        }

        $yibiao_sn = Db::name('yibiao')
            ->where('yibiao_id',$data['yibiao_id'])
            ->where('xqgl_id',session('shop.xqgl_id'))
            ->value('yibiao_sn');

        if ($yibiao_sn != $data['yibiao_sn']) {

            $yibiao_is_not = Db::name('yibiao')
                ->where('yibiao_sn',$data['yibiao_sn'])
                ->where('xqgl_id',session('shop.xqgl_id'))->find();

            if (!empty($yibiao_is_not)) {
                return json(['status'=>201,'msg'=>'已存在该仪表编号']);
            }
        }

        if ($data['yibiao_status'] == 1) {
            // 房间是否已分配该种类仪表
            /*$fcxx_ybzl_id = Db::name('fcxx')->alias('a')
                ->leftJoin('yibiao b','a.fcxx_id=b.fcxx_id')
                ->where('a.fcxx_id',$data['fcxx_id'])
                ->where('b.ybzl_id',$data['ybzl_id'])
                ->find();

            if (!empty($fcxx_ybzl_id)) {
                return json(['status'=>201,'msg'=>'该房间已绑定该种类仪表']);
            }*/

            // 仪表是否已分配
            $yibiao_fcxx_id = Db::name('yibiao')
                ->where('yibiao_status',1)
                ->where('yibiao_id',$data['yibiao_id'])
                ->value('fcxx_id');

            if (!empty($yibiao_fcxx_id)) {
//                dump($yibiao_fcxx_id);exit; // 房间id
                $cbgl = Db::name('cbgl')
                    ->where('fcxx_id',$yibiao_fcxx_id)
                    ->where('yibiao_id',$data['yibiao_id'])->find();

                if (!empty($cbgl)) {
                    $yssj = Db::name('yssj')
                        ->where('cbgl_id',$cbgl['cbgl_id'])->find();

                    if (!empty($yssj)) {

                        return json(['status'=>201,'msg'=>'该仪表已产生费用,不可更改']);
                    }
                }
            }
        } else {
            // 该仪表抄表数量
            if (empty($data['cbgl_bqsl'])) {
                return json(['status'=>201,'msg'=>'抄表数值必填']);
            }
        }

    }

    function afterShopUpdate($data) {

        /*[
            "yibiao_id" => 5,
            "shop_id" => 1,
            "xqgl_id" => 9,
            "yibiao_sn" => "202212300809",
            "yblx_id" => 1,
            "ybzl_id" => 2,
            "louyu_id" => 118, // 单元
            "fcxx_id" => 3008,
            "yibiao_ybbl" => 1,
            "yibiao_csds" => 0,
            "yibiao_yblc" => 999999,
            "add_time" => 1672358940,
            "yibiao_status" => 2,
            "yibiao_remarks" => "",
            "cbgl_bqsl" => 900,
        ];

        [
          "shop_id" => 1,
          "xqgl_id" => 9,
          "cbpc_cwyf" => "2023-01",
          "cbpc_kstime" => 1674230400,
          "cbpc_jstime" => 1674316800,
          "louyu_id" => 117,
          "yblx_id" => 1,
          "ybzl_id" => 2,
          "cbpc_id" => 2,
        ];*/

        if ($data['yibiao_status'] != 1) {
            // 生成抄表管理数据
//            $time = time();
//            $cbpc_cwyf = date('Y-m',time());

            $time = $data['yibiao_hbsj'];
            $cbpc_cwyf = date('Y-m',$time);

            $louyu_id = Db::name('louyu')->where('louyu_id',$data['louyu_id'])->value('louyu_pid');

            $where = [];
            $where[] = ['louyu_id','=', $louyu_id];
            $where[] = ['xqgl_id', '=', $data['xqgl_id']];
            $where[] = ['yblx_id', '=', $data['yblx_id']];
            $where[] = ['ybzl_id', '=', $data['ybzl_id']];
            $cbpc_jstime = Db::name('cbpc')->where($where)->order('cbpc_id','desc')->value('cbpc_jstime');

            $data['yibiao_status'];

            if ($data['yibiao_status'] == 0) {
                $cbpc_ghcb = 1;
            } else {
                $cbpc_ghcb = 0;
            }

            $cbpc_data = [
                'shop_id' => $data['shop_id'],
                'xqgl_id' => $data['xqgl_id'],
                'cbpc_cwyf' => $cbpc_cwyf,
                'cbpc_kstime' => $cbpc_jstime,
                'cbpc_jstime' => $time,
                'louyu_id' => $louyu_id,
                'yblx_id' => $data['yblx_id'],
                'ybzl_id' => $data['ybzl_id'],
                'cbpc_ghcb' => $cbpc_ghcb,
                'cbpc_status' => 0
            ];

            $cbpc_id = Db::name('cbpc')->insertGetId($cbpc_data);

            $cbpc_data = array_merge($cbpc_data,[
                'cbpc_id'       =>$cbpc_id,
                'yibiao_id'     => $data['yibiao_id'],
                'yibiao_sn'     =>$data['yibiao_sn'],
                'yibiao_ybbl'   =>$data['yibiao_ybbl'],
                'cbgl_bqsl'     => $data['cbgl_bqsl'],
            ]);

            $this->addCbgl($cbpc_data,$louyu_id,$data['fcxx_id']);
        }

        return json(['status'=>200,'data'=>$data,'msg'=>'操作成功']);

    }


    function addCbgl($data,$louyu_id,$fcxx_id) {

        $member_id = Db::name('fcxx')->where('fcxx_id',$fcxx_id)->value('member_id');

        $before_cbgl_data = Db::name('cbgl')
            ->where('fcxx_id',$fcxx_id)
            ->where('ybzl_id',$data['ybzl_id'])
            ->where('yblx_id',$data['yblx_id'])
            ->where('cbgl_jstime','<',$data['cbpc_kstime'])
            ->order('cbgl_id', 'desc')
            ->find();
        $cbgl_bqsl = $before_cbgl_data['cbgl_bqsl']??0; // 上期数量

        $fydy_where = [];
        $fydy_where[] = ['shop_id','=',session('shop.shop_id')];
        $fydy_where[] = ['xqgl_id','=',session('shop.xqgl_id')];
        $fydy_where[] = ['fylx_id','=',2]; // 费用类型：仪表类费用
        if ($data['ybzl_id'] == 2) { // 水表

            $fydy_where[] = ['fylb_id','=',2]; // 水费;

        } elseif ($data['ybzl_id'] == 3) { // 电表

            $fydy_where[] = ['fylb_id','=',6]; // 电费
        }
        $fydy = Db::name('fydy')->where($fydy_where)->find();

        $where_fyfp = [];
        $where_fyfp[] = ['fcxx_id','=',$fcxx_id];
        $where_fyfp[] = ['fydy_id','=',$fydy['fydy_id']];
        $fyfp = Db::name('fyfp')->where($where_fyfp)->find();


        $fybz = Db::name('fybz')->where('fybz_id',$fyfp['fybz_id'])->find();

        $qzfs = Db::name('fybz')->alias('a')
            ->field('c.*')
            ->leftJoin('fydy b','a.fydy_id=b.fydy_id')
            ->leftJoin('qzfs c','b.qzfs_id=c.qzfs_id')
            ->where('fybz_id',$fybz['fybz_id'])
            ->find();

        $cbgl_cbyl = $data['cbgl_bqsl'] - $cbgl_bqsl;

        $cbgl_cbje = $cbgl_cbyl * $data['yibiao_ybbl'] * $fybz['fybz_bzdj'];

        if ($qzfs['qzfs_qzws'] == 0) {
            $cbgl_cbje = intval(round($cbgl_cbje));
        } else {
            $cbgl_cbje = round($cbgl_cbje, $qzfs['qzfs_qzws']);
        }

        $cbgl_data = [
            'cbpc_id'       => $data['cbpc_id'],
            'cbgl_kstime'   => $data['cbpc_kstime'],
            'cbgl_jstime'   => $data['cbpc_jstime'],
            'cbgl_cwyf'     => $data['cbpc_cwyf'],
            'louyu_id'      => $louyu_id,
            'yblx_id'       => $data['yblx_id'],
            'ybzl_id'       => $data['ybzl_id'],
            'fcxx_id'       => $fcxx_id,
            'member_id'     => $member_id, // 住户姓名
            'yibiao_id'     => $data['yibiao_id'], // 仪表编号
            'yibiao_sn'     => $data['yibiao_sn'], // 仪表编号
            'cbgl_sqsl'     => $cbgl_bqsl, // 上期数量
            'cbgl_bqsl'     => $data['cbgl_bqsl'], // 本期数量
            'cbgl_cbyl'     => $cbgl_cbyl, // 本期数量 - 上期数量
            'cbgl_shyl'     => 0, // 损耗用量
            'cbgl_ybbl'     => $data['yibiao_ybbl'], // 仪表倍率
            'cbgl_sjyl'     => $cbgl_cbyl, // 本期数量 - 上期数量
            'shop_id'       => session('shop.shop_id'),
            'xqgl_id'       => session('shop.xqgl_id'),
            'fybz_bzdj'     => $fybz['fybz_bzdj'], // 标准单价
            'cbgl_cbje'     => $cbgl_cbje, // 用量*倍数*单价
            'fybz_id'       => $fybz['fybz_id'],    // 标准单价,
            'cbgl_status'   => 0,
            'fybz_name'     => $fybz['fybz_name']
        ];

        Db::name('cbgl')->insert($cbgl_data);
    }
}