<?php
namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;
use support\Log;
use think\facade\Db;
use app\shop\model\Cbgl;

class Cbpc
{
    function beforShopAdd($data) {

        if ($data['cbpc_kstime'] > $data['cbpc_jstime']) {

            return json(['status'=>201,'msg'=>'结束日期应大于开始日期']);
        }

        if ($data['cbpc_jstime'] > time()) {
            return json(['status'=>201,'msg'=>'生成时间不能大于当前时间']);
        }

        $where = [];
        $where[] = ['louyu_id','=', $data['louyu_id']];
        $where[] = ['xqgl_id', '=', $data['xqgl_id']];
        $where[] = ['yblx_id', '=', $data['yblx_id']];
        $where[] = ['ybzl_id', '=', $data['ybzl_id']];

        $cbpc_jstime = Db::name('cbpc')
            ->where($where)
            ->where('cbpc_ghcb',0)
            ->order('cbpc_id','desc')
            ->value('cbpc_jstime');

        if (!empty($cbpc_jstime)) {

            if ($data['cbpc_kstime'] <= $cbpc_jstime) {
                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbpc_jstime))."开始"
                ]);
            }

            if ($data['cbpc_kstime'] != strtotime('+1 day', $cbpc_jstime)) {

                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbpc_jstime))."开始"
                ]);
            }

        }

    }

    function afterShopAdd($data) {

        // 该楼宇所有 单元信息ID
        $dyxx = Db::name('louyu')->where('louyu_pid',$data['louyu_id'])->column('louyu_id');
        $dyxx[] = $data['louyu_id'];
        $where_fcxx = [];
        $where_fcxx[] = ['louyu_id','in',$dyxx];
        $fcxx = Db::name('fcxx')->where($where_fcxx)->select(); // 楼宇所有房间信息

        $cbgl_data = [];
        foreach ($fcxx as $fcxx_item) {

            if (empty($fcxx_item['member_id'])) { // 房间未入住
                continue;
            }

            $yibiao_where = [];
            $yibiao_where[] = ['fcxx_id','=',$fcxx_item['fcxx_id']];
            $yibiao_where[] = ['yibiao_status','=',1];
            $yibiao_where[] = ['ybzl_id','=',$data['ybzl_id']]; // 本次抄表的仪表种类

            $yibiao = Db::name('yibiao')->where($yibiao_where)->select(); // 获取房间关联的仪表

            if (empty($yibiao)) {
                continue;
            }
            foreach ($yibiao as $yibiao_item) {

                $before_cbgl_data = Db::name('cbgl')
                    ->where('yibiao_id',$yibiao_item['yibiao_id'])
                    ->where('fcxx_id',$fcxx_item['fcxx_id'])
                    ->where('ybzl_id',$data['ybzl_id'])
                    ->where('yblx_id',$data['yblx_id'])
    //                ->where('cbgl_jstime','<',$data['cbpc_kstime'])
                    ->order('cbgl_id', 'desc')
                    ->find();

                $cbgl_bqsl = $before_cbgl_data['cbgl_bqsl']??$yibiao_item['yibiao_csds']; // 上期数量

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
                $where_fyfp[] = ['fcxx_id','=',$fcxx_item['fcxx_id']];
                $where_fyfp[] = ['fydy_id','=',$fydy['fydy_id']];
                $fyfp = Db::name('fyfp')->where($where_fyfp)->find();

                $fybz = Db::name('fybz')->where('fybz_id',$fyfp['fybz_id'])->find();
                $cbgl_data[] = [
                    'cbpc_id'       => $data['cbpc_id'],
                    'cbgl_kstime'   => $before_cbgl_data['cbgl_jstime'],
                    'cbgl_jstime'   => $data['cbpc_jstime'],
                    'cbgl_cwyf'     => $data['cbpc_cwyf'],
                    'louyu_id'      => $fcxx_item['louyu_id'],
                    'yblx_id'       => $data['yblx_id'],
                    'ybzl_id'       => $data['ybzl_id'],
                    'fcxx_id'       => $fcxx_item['fcxx_id'],
                    'member_id'     => $fcxx_item['member_id'], // 住户姓名
                    'yibiao_id'     => $yibiao_item['yibiao_id'], // 仪表编号
                    'yibiao_sn'     => $yibiao_item['yibiao_sn'], // 仪表编号
                    'cbgl_sqsl'     => $cbgl_bqsl, // 上期数量
                    'cbgl_bqsl'     => 0, // 本期数量
                    'cbgl_cbyl'     => 0, // 本期数量 - 上期数量
                    'cbgl_shyl'     => 0, // 损耗用量
                    'cbgl_ybbl'     => $yibiao_item['yibiao_ybbl'], // 仪表倍率
                    'cbgl_sjyl'     => 0, // 本期数量 - 上期数量 - 损耗用量
                    'shop_id'       => session('shop.shop_id'),
                    'xqgl_id'       => session('shop.xqgl_id'),
                    'fybz_bzdj'     => $fybz['fybz_bzdj'], // 标准单价
                    'cbgl_cbje'     => 0, // 用量*倍数*单价
                    'fybz_id'       => $fybz['fybz_id'],    // 标准单价,
                    'cbgl_status'   => 0,
                    'fybz_name'     => $fybz['fybz_name']
                ];

            }

        }

        $CbglModel =  new Cbgl();

        $CbglModel->saveAll($cbgl_data);

        Db::name('cbpc')->where('cbpc_id',$data['cbpc_id'])->update(['cbpc_status' => 0]);

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

    function beforShopUpdate($data) {

        if ($data['cbpc_kstime'] > $data['cbpc_jstime']) {

            return json(['status'=>201,'msg'=>'结束日期应大于开始日期']);
        }

        $louyu_id = Db::name('cbpc')->where('cbpc_id',$data['cbpc_id'])->value('louyu_id');

        $where = [];
        $where[] = ['louyu_id','=', $louyu_id];
        $where[] = ['xqgl_id', '=', $data['xqgl_id']];
        $where[] = ['yblx_id', '=', $data['yblx_id']];
        $where[] = ['ybzl_id', '=', $data['ybzl_id']];
        $where[] = ['cbpc_id', '<',$data['cbpc_id']];

        $cbpc_jstime = Db::name('cbpc')
            ->where($where)
            ->where('cbpc_ghcb',0)
            ->order('cbpc_id','desc')
            ->value('cbpc_jstime');

        if (!empty($cbpc_jstime)) {

            if ($data['cbpc_kstime'] <= $cbpc_jstime) {

                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbpc_jstime))."开始"
                ]);
            }

            if (date('Y-m-d',$data['cbpc_kstime']) != date('Y-m-d',strtotime('+1 day', $cbpc_jstime))) {

                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbpc_jstime))."开始"
                ]);
            }

        }

    }

    function afterShopUpdate($data) {

        //[
        //  "cbpc_id" => 39
        //  "shop_id" => 15
        //  "xqgl_id" => 26
        //  "cbpc_cwyf" => "2023-03"
        //  "cbpc_kstime" => 1677600000
        //  "cbpc_jstime" => 1680192000
        //  "yblx_id" => 1
        //  "ybzl_id" => 2
        //]

        $cbpc = Db::name('cbpc')->where('cbpc_id',$data['cbpc_id'])->find();

        $louyu_id = $cbpc['louyu_id'];

        // 本批次明细
        $cbgl = Db::name('cbgl')->where('cbpc_id',$data['cbpc_id'])->select();

        $dyxx = Db::name('louyu')->where('louyu_pid',$louyu_id)->column('louyu_id');
        $dyxx[] = $louyu_id; // 楼宇和单元id

        $cbgl_data_update = []; // 更新数据集 明细
        $existing_fcxx_id = []; // 更新数据集 房产
        $existing_yibiao_id = []; // 更新数据集 房产

        foreach ($cbgl as $cbgl_item) {

            $yibiao_where = [];
            $yibiao_where[] = ['ybzl_id','=',$data['ybzl_id']];
            $yibiao_where[] = ['yibiao_id','=',$cbgl_item['yibiao_id']];
            $yibiao_where[] = ['fcxx_id','=',$cbgl_item['fcxx_id']];
//            $yibiao = Db::name('yibiao')->where($yibiao_where)->find();

            if ($cbgl->count() == 1) {

                $yibiao_where[] = ['yibiao_status','=',2];
                $yibiao = Db::name('yibiao')->where($yibiao_where)->order('yibiao_id','desc')->find();

            } else {

                $yibiao_where[] = ['yibiao_status','=',1];
                $yibiao = Db::name('yibiao')->where($yibiao_where)->find();
                if (empty($yibiao)) {
                    $cbgl_select = Db::name('cbgl')->where('cbgl_id',$cbgl_item['cbgl_id'])->find();
                    if ($cbgl_select) {
//                    dump($cbgl_select);
                        Db::name('cbgl')->where('cbgl_id',$cbgl_item['cbgl_id'])->delete();
                    }
                    continue;
                }
            }

            $existing_fcxx_id[] = $cbgl_item['fcxx_id'];
            $existing_yibiao_id[] = $yibiao['yibiao_id'];

            $before_cbgl_data = Db::name('cbgl') // afterShopUpdate 改
                ->where('cbpc_id','<',$data['cbpc_id'])
                ->where('yibiao_id',$yibiao['yibiao_id'])
                ->where('fcxx_id',$cbgl_item['fcxx_id'])
                ->where('ybzl_id',$data['ybzl_id'])
                ->where('yblx_id',$data['yblx_id'])
                ->order('cbgl_id', 'desc')
                ->find();

            $cbgl_bqsl = $before_cbgl_data['cbgl_bqsl']??$yibiao['yibiao_csds']; // 上期数量

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
            $where_fyfp[] = ['fcxx_id','=',$cbgl_item['fcxx_id']];
            $where_fyfp[] = ['fydy_id','=',$fydy['fydy_id']];
            $fyfp = Db::name('fyfp')->where($where_fyfp)->find();

            $fybz = Db::name('fybz')->where('fybz_id',$fyfp['fybz_id'])->find();
            $fcxx_member_id = Db::name('fcxx')->where('fcxx_id',$cbgl_item['fcxx_id'])->value('member_id');

            $cbgl_data_update[] = [
                'cbgl_id'       => $cbgl_item['cbgl_id'],
                'cbpc_id'       => $data['cbpc_id'],
                'cbgl_kstime'   => !empty($before_cbgl_data['cbgl_jstime'])?$before_cbgl_data['cbgl_jstime']+86400:$data['cbpc_kstime'],
                'cbgl_jstime'   => $data['cbpc_jstime'],
                'cbgl_cwyf'     => $data['cbpc_cwyf'],
                'fcxx_id'       => $cbgl_item['fcxx_id'],
                'member_id'     => $fcxx_member_id, // 住户姓名
                'yibiao_id'     => $yibiao['yibiao_id'], // 仪表编号
                'yibiao_sn'     => $yibiao['yibiao_sn'], // 仪表编号
                'cbgl_sqsl'     => $cbgl_bqsl, // 上期数量
                'cbgl_bqsl'     => 0, // 本期数量
                'cbgl_cbyl'     => 0, // 本期数量 - 上期数量
                'cbgl_shyl'     => 0, // 损耗用量
                'cbgl_ybbl'     => $yibiao['yibiao_ybbl'], // 仪表倍率
                'cbgl_sjyl'     => 0, // 本期数量 - 上期数量 - 损耗用量
                'shop_id'       => session('shop.shop_id'),
                'xqgl_id'       => session('shop.xqgl_id'),
                'fybz_bzdj'     => $fybz['fybz_bzdj'], // 标准单价
                'cbgl_cbje'     => 0, // 用量*倍数*单价
                'fybz_id'       => $fybz['fybz_id'],
                'cbgl_status'   => 0,
                'fybz_name'     => $fybz['fybz_name']
            ];
        }

        if (!empty($cbgl_data_update)) {
            $CbglModel =  new Cbgl();
            $CbglModel->saveAll($cbgl_data_update);
        }

        if ($cbgl->count() == 1) {
            return json(['status'=>200,'msg'=>'修改成功']);
        } else {
            // 楼宇所有房间信息
            $where_fcxx = [];
            $where_fcxx[] = ['louyu_id','in',$dyxx];
            $fcxx = Db::name('fcxx')->where($where_fcxx)->select();

            $cbgl_data = [];
            foreach ($fcxx as $fcxx_item) {

                if (empty($fcxx_item['member_id'])) { // 房间未入住
                    continue;
                }

                $yibiao_where = [];
                $yibiao_where[] = ['fcxx_id','=',$fcxx_item['fcxx_id']];
                $yibiao_where[] = ['yibiao_status','=',1];
                $yibiao_where[] = ['ybzl_id','=',$data['ybzl_id']]; // 本次抄表的仪表种类
                $yibiao = Db::name('yibiao')->where($yibiao_where)->select(); // 获取房间关联的仪表

                if (empty($yibiao)) { // 房间未分配仪表
                    continue;
                }

                foreach ($yibiao as $yibiao_item) {

                    if (in_array($yibiao_item['yibiao_id'],$existing_yibiao_id)) {
                        if (in_array($fcxx_item['fcxx_id'],$existing_fcxx_id)) { // 已存在抄表信息
                            continue;
                        }
                    }

                    $before_cbgl_data = Db::name('cbgl') // afterShopUpdate 加
                        ->where('cbpc_id','<',$data['cbpc_id'])
                        ->where('yibiao_id',$yibiao_item['yibiao_id'])
                        ->where('fcxx_id',$fcxx_item['fcxx_id'])
                        ->where('ybzl_id',$data['ybzl_id'])
                        ->where('yblx_id',$data['yblx_id'])
                        ->order('cbgl_id', 'desc')
                        ->find();

                    $cbgl_bqsl = $before_cbgl_data['cbgl_bqsl']??$yibiao_item['yibiao_csds']; // 上期数量

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
                    $where_fyfp[] = ['fcxx_id','=',$fcxx_item['fcxx_id']];
                    $where_fyfp[] = ['fydy_id','=',$fydy['fydy_id']];
                    $fyfp = Db::name('fyfp')->where($where_fyfp)->find();

                    $fybz = Db::name('fybz')->where('fybz_id',$fyfp['fybz_id'])->find();

                    $cbgl_data[] = [
                        'cbpc_id'       => $data['cbpc_id'],
                        'cbgl_kstime'   => !empty($before_cbgl_data['cbgl_jstime'])?$before_cbgl_data['cbgl_jstime']+86400:$data['cbpc_kstime'],
                        'cbgl_jstime'   => $data['cbpc_jstime'],
                        'cbgl_cwyf'     => $data['cbpc_cwyf'],
                        'louyu_id'      => $data['louyu_id'],
                        'yblx_id'       => $data['yblx_id'],
                        'ybzl_id'       => $data['ybzl_id'],
                        'fcxx_id'       => $fcxx_item['fcxx_id'],
                        'member_id'     => $fcxx_item['member_id'], // 住户姓名
                        'yibiao_id'     => $yibiao_item['yibiao_id'], // 仪表编号
                        'yibiao_sn'     => $yibiao_item['yibiao_sn'], // 仪表编号
                        'cbgl_sqsl'     => $cbgl_bqsl, // 上期数量
                        'cbgl_bqsl'     => 0, // 本期数量
                        'cbgl_cbyl'     => 0, // 本期数量 - 上期数量
                        'cbgl_shyl'     => 0, // 损耗用量
                        'cbgl_ybbl'     => $yibiao_item['yibiao_ybbl'], // 仪表倍率
                        'cbgl_sjyl'     => 0, // 本期数量 - 上期数量 - 损耗用量
                        'shop_id'       => session('shop.shop_id'),
                        'xqgl_id'       => session('shop.xqgl_id'),
                        'fybz_bzdj'     => $fybz['fybz_bzdj'], // 标准单价
                        'cbgl_cbje'     => 0, // 用量*倍数*单价
                        'fybz_id'       => $fybz['fybz_id'],
                        'cbgl_status'   => 0,
                        'fybz_name'     => $fybz['fybz_name']
                    ];

                }

            }

            if (!empty($cbgl_data)) {
                $CbglModel =  new Cbgl();
                $CbglModel->saveAll($cbgl_data);
            }

            return json(['status'=>200,'msg'=>'修改成功']);
        }

    }

    function beforShopDelete($data){

        $cbgl_ids = Db::name('cbgl')->where('cbpc_id',$data)
            ->where('cbgl_status',1)
            ->count();

        if (!empty($cbgl_ids)) {
            return json(['status'=>201,'msg'=>'存在入账抄表']);
        }

    }

    function afterShopDelete($data) {

        Db::name('cbgl')->where('cbpc_id',$data)->delete();

        return json(['status'=>200,'msg'=>'操作成功']);
    }

    function beforShopPlrz($data) {

        $cbgl = Db::name('cbgl')->where('cbpc_id',$data)
            ->where('cbgl_sqsl','<>',0)
            ->where('cbgl_bqsl',0)
            ->select()->toArray();

        if (!empty($cbgl)) {
            return json(['status'=>201,'msg'=>'本批次，有未抄表的本期数量']);
        }

    }

    function afterShopPlrz($data) {

        Db::name('cbgl')->where('cbpc_id',$data)->update(['cbgl_status' => 1]);

        $cbgl = Db::name('cbgl')->where('cbpc_id',$data)->select();

        $fydy_id = Db::name('fybz')->where('fybz_id',$cbgl['fybz_id'])->value('fydy_id');

        $yssj_data = [];
        $member_id_column = [];

        foreach ($cbgl as $cbgl_item) {
            $yssj = Db::name('yssj')->where('cbgl_id',$cbgl_item['cbgl_id'])->select()->toArray();

            if (!empty($yssj)) {
                continue;
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

    function beforShopAloneAdd($data) {

        if ($data['cbpc_jstime'] > time()) {
            return json(['status'=>201,'msg'=>'生成时间不能大于当前时间']);
        }

        if ($data['cbpc_kstime'] > $data['cbpc_jstime']) {

            return json(['status'=>201,'msg'=>'结束日期应大于开始日期']);
        }

        $cbgl_jstime = Db::name('cbgl') // 最后一次抄表时间
            ->where('fcxx_id',$data['fcxx_id'])
            ->order('cbgl_id','desc')->value('cbgl_jstime');

        if (!empty($cbgl_jstime)) {

            if ($data['cbpc_kstime'] <= $cbgl_jstime) {
                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbgl_jstime))."开始"
                ]);
            }

            if ($data['cbpc_kstime'] != strtotime('+1 day', $cbgl_jstime)) {

                return json([
                    'status'=>201,
                    'msg'=>"抄表日期应从 ".date('Y-m-d',strtotime('+1 day', $cbgl_jstime))."开始"
                ]);
            }
        }

    }

    function afterShopAloneAdd($data)
    {
        $fcxx = Db::name('fcxx')->where('fcxx_id', $data['fcxx_id'])->find(); // 房间信息

        $cbgl_data = [];

        if (!empty($fcxx['member_id'])) { // 房间未入住

            $yibiao_where = [];
            $yibiao_where[] = ['fcxx_id', '=', $fcxx['fcxx_id']];
            $yibiao_where[] = ['yibiao_status', '=', 1];
            $yibiao_where[] = ['ybzl_id', '=', $data['ybzl_id']]; // 本次抄表的仪表种类
            $yibiao = Db::name('yibiao')->where($yibiao_where)->find(); // 获取房间关联的仪表

            if (!empty($yibiao)) {

                $before_cbgl_data = Db::name('cbgl') // 该仪表最近一次的本期抄表数 afterShopAloneAdd
                    ->where('yibiao_id',$yibiao['yibiao_id']) //
                    ->where('fcxx_id', $fcxx['fcxx_id'])
                    ->where('ybzl_id', $data['ybzl_id'])
                    ->where('yblx_id', $data['yblx_id'])
//                    ->where('cbgl_jstime', '<', $data['cbpc_kstime'])
                    ->order('cbgl_id', 'desc')
                    ->find();

                $cbgl_bqsl = $before_cbgl_data['cbgl_bqsl'] ?? 0; // 上期数量

                $fydy_where = [];
                $fydy_where[] = ['shop_id', '=', session('shop.shop_id')];
                $fydy_where[] = ['xqgl_id', '=', session('shop.xqgl_id')];
                $fydy_where[] = ['fylx_id', '=', 2]; // 费用类型：仪表类费用
                if ($data['ybzl_id'] == 2) { // 水表

                    $fydy_where[] = ['fylb_id', '=', 2]; // 水费;

                } elseif ($data['ybzl_id'] == 3) { // 电表

                    $fydy_where[] = ['fylb_id', '=', 6]; // 电费
                }

                $fydy = Db::name('fydy')->where($fydy_where)->find();

                $where_fyfp = [];
                $where_fyfp[] = ['fcxx_id', '=', $fcxx['fcxx_id']];
                $where_fyfp[] = ['fydy_id', '=', $fydy['fydy_id']];
                $fyfp = Db::name('fyfp')->where($where_fyfp)->find();

                $fybz = Db::name('fybz')->where('fybz_id', $fyfp['fybz_id'])->find();

                $cbgl_data[] = [
                    'cbpc_id' => $data['cbpc_id'],
                    'cbgl_kstime' => $data['cbpc_kstime'],
                    'cbgl_jstime' => $data['cbpc_jstime'],
                    'cbgl_cwyf' => $data['cbpc_cwyf'],
                    'louyu_id' => $fcxx['louyu_id'],
                    'yblx_id' => $data['yblx_id'],
                    'ybzl_id' => $data['ybzl_id'],
                    'fcxx_id' => $fcxx['fcxx_id'],
                    'member_id' => $fcxx['member_id'], // 住户姓名
                    'yibiao_id' => $yibiao['yibiao_id'], // 仪表编号
                    'yibiao_sn' => $yibiao['yibiao_sn'], // 仪表编号
                    'cbgl_sqsl' => $cbgl_bqsl, // 上期数量
                    'cbgl_bqsl' => 0, // 本期数量
                    'cbgl_cbyl' => 0, // 本期数量 - 上期数量
                    'cbgl_shyl' => 0, // 损耗用量
                    'cbgl_ybbl' => $yibiao['yibiao_ybbl'], // 仪表倍率
                    'cbgl_sjyl' => 0, // 本期数量 - 上期数量 - 损耗用量
                    'shop_id' => session('shop.shop_id'),
                    'xqgl_id' => session('shop.xqgl_id'),
                    'fybz_bzdj' => $fybz['fybz_bzdj'], // 标准单价
                    'cbgl_cbje' => 0, // 用量*倍数*单价
                    'fybz_id' => $fybz['fybz_id'],    // 标准单价,
                    'cbgl_status' => 0,
                    'fybz_name' => $fybz['fybz_name']
                ];
            }

        }

        if (!empty($cbgl_data)) {
            $CbglModel =  new Cbgl();

            $CbglModel->saveAll($cbgl_data);
        }

        Db::name('cbpc')->where('cbpc_id', $data['cbpc_id'])->update(['cbpc_status' => 0]);

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

    function addDanHuCbgl($data,$cbgl) {

    }
}