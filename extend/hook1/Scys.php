<?php

namespace hook;

use app\shop\model\Yssj;
use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Scys
{
    function beforShopAdd($data) {

        if ($data['scys_scfs'] == 1) {

            $scys_ksyf_s = date('Y-m-01',$data['scys_ksyf']);
            $scys_jsyf_s = date('Y-m-01',$data['scys_jsyf']);

            $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($scys_ksyf_s) : ''; // 开始月份
            $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime("$scys_jsyf_s +1 month -1 day") : ''; // 终止月份

            if ($data['scys_ksyf'] > $data['scys_jsyf']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $scys_jsyf = Db::name('scys')
                ->where('scys_scgc',1)
                ->where('louyu_id',$data['louyu_id'])
                ->order('scys_id','desc')->find();

            if (!empty($scys_jsyf)) {
                if ($data['scys_kstime'] != strtotime('+1 day', $scys_jsyf['scys_jsyf'])) {
                    return json([
                        'status'=>201,
                        'msg'=>"日期应从 ".date('Y-m-d',strtotime('+1 day', $scys_jsyf['scys_jsyf']))."开始"
                    ]);
                }
            }

        }

        if ($data['scys_scfs'] == 2) {

            $data['scys_kstime'] = !empty($data['scys_kstime']) ? $data['scys_kstime'] : ''; // 开始时间
            $data['scys_zztime'] = !empty($data['scys_zztime']) ? $data['scys_zztime'] : ''; // 终止时间

            if ($data['scys_kstime'] > $data['scys_zztime']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $scys_jsyf = Db::name('scys')
                ->where('scys_scgc',1)
                ->where('louyu_id',$data['louyu_id'])
                ->order('scys_id','desc')->find();
            if (!empty($scys_jsyf)) {
                if ($data['scys_kstime'] != strtotime('+1 day', $scys_jsyf['scys_jsyf'])) {
                    return json([
                        'status'=>201,
                        'msg'=>"日期应从 ".date('Y-m-d',strtotime('+1 day', $scys_jsyf['scys_jsyf']))."开始"
                    ]);
                }
            }

        }

    }

    function afterShopAdd($data) {

        $fydy_id_arr = explode(',',$data['fydy_id']);

        // 获取费用标准
        $fybz = Db::name('fybz')
            ->field('fylx_id,fybz_id,fydy_id,fybz_name,fybz_bzdj')
            ->whereIn('fydy_id',$fydy_id_arr)
            ->select();

        $where_fyfp_fcxx = [];

        if (!empty($data['louyu_id'])) {
            $danyuan_ids = Db::name('louyu')->where('louyu_pid',$data['louyu_id'])->column('louyu_id');

            $danyuan_ids[] = $data['louyu_id'];

            $where_fyfp_fcxx[] = ['a.louyu_id','in',$danyuan_ids];
        }

        $member_id_arr = [];
        foreach ($fybz as $fybz_item) {

            $where_aa = [
                'c.fybz_id' => $fybz_item['fybz_id'],
                'b.shop_id' => session('shop.shop_id'),
                'b.xqgl_id' => session('shop.xqgl_id'),
            ];

            $fyfp_fcxx = Db::name('louyu')->alias('a')
                ->leftJoin('fcxx b','a.louyu_id=b.louyu_id')
                ->leftJoin('fyfp c','b.fcxx_id=c.fcxx_id')
                ->leftJoin('fybz d','c.fybz_id=d.fybz_id')
                ->field("c.*,b.fcxx_jzmj,b.member_id,d.fybz_bzdj,d.fybz_name,d.fybz_id,d.jfgs_id")
                ->where($where_aa)
                ->where($where_fyfp_fcxx)
                ->select();

            $yssj_data = [];

            if (!empty($fyfp_fcxx)) {
                foreach ($fyfp_fcxx as $fyfp_fcxx_item) {

                    if ($fyfp_fcxx_item['member_id'] == 0) {
                        continue;
                    }

                    $member_id_arr[] = $fyfp_fcxx_item['member_id'];

                    $qzfs = Db::name('qzfs')->alias('a')
                        ->leftJoin('fydy b','a.qzfs_id= b.qzfs_id')
                        ->where('b.fydy_id',$fybz_item['fydy_id'])
                        ->find();

                    if ($data['scys_scfs'] == 1) { // 生成方式 按月生成

                        $yssj_cc = Db::name('yssj')
                            ->where('fcxx_id',$fyfp_fcxx_item['fcxx_id'])
                            ->where('fybz_id',$fybz_item['fybz_id'])
                            ->where('yssj_kstime','>=',$data['scys_ksyf'])
                            ->where('yssj_jztime','<=',$data['scys_jsyf'])->select()->toArray();

                        if (!empty($yssj_cc)) {
                            continue;
                        }

                        $calculate = $this->calculateMonthScys($data);

                        $yssj_ysje = 0;
                        if ($fyfp_fcxx_item['jfgs_id'] == 1) { // 1)单价

                            $yssj_ysje = $fyfp_fcxx_item['fybz_bzdj'];

                        } elseif ($fyfp_fcxx_item['jfgs_id'] == 2) { // 2)单价*使用面积

                            $yssj_ysje = $fyfp_fcxx_item['fybz_bzdj']*$fyfp_fcxx_item['fcxx_jzmj'];

                        }

                        if ($qzfs['qzfs_qzws'] == 0) {

                            $yssj_ysje = intval(round($yssj_ysje));

                        } else {

                            $yssj_ysje = round($yssj_ysje, $qzfs['qzfs_qzws']);

                        }


                        foreach ($calculate as $calculate_item) {

                            $yssj_data[] = [
                                'scys_id'       => $data['scys_id'],
                                'yssj_fymc'     => $fybz_item['fybz_name'],
                                'fydy_id'       => $fybz_item['fydy_id'],
                                'yssj_cwyf'     => $calculate_item['cwyf'],
                                'yssj_kstime'   => $calculate_item['kstime'],
                                'yssj_jztime'   => $calculate_item['jztime'],
                                'yssj_fydj'     => $fybz_item['fybz_bzdj'],
                                'yssj_ysje'     => $yssj_ysje,
                                'fylx_id'       => 1,
                                'fybz_id'       => $fybz_item['fybz_id'],
                                'yssj_stuats'   => 0,
                                'yssj_fksj'     => '',
                                'shop_id'       => session('shop.shop_id'),
                                'xqgl_id'       => session('shop.xqgl_id'),
                                'member_id'     => $fyfp_fcxx_item['member_id'],
                                'syt_id'        => null,
                                'fcxx_id'       => $fyfp_fcxx_item['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                    if ($data['scys_scfs'] == 2) { // 生成方式 按日生成


                        $yssj_cc = Db::name('yssj')
                            ->where('fcxx_id',$fyfp_fcxx_item['fcxx_id'])
                            ->where('fybz_id',$fybz_item['fybz_id'])
                            ->where('yssj_kstime','>=',$data['scys_kstime'])
                            ->where('yssj_jztime','<=',$data['scys_zztime'])->select()->toArray();

                        if (!empty($yssj_cc)) {
                            continue;
                        }

                        $calculate = $this->calculateDaysScys($data,$fyfp_fcxx_item);

                        foreach ($calculate as $calculate_item) {

                            $yssj_ysje = 0;

                            if ($fyfp_fcxx_item['jfgs_id'] == 1) { // 1)单价

                                $yssj_ysje = $calculate_item['ysje'];

                            } elseif ($fyfp_fcxx_item['jfgs_id'] == 2) { // 2)单价*使用面积

                                $yssj_ysje = $calculate_item['ysje']*$fyfp_fcxx_item['fcxx_jzmj'];
                            }

                            if ($qzfs['qzfs_qzws'] == 0) {

                                $yssj_ysje = intval(round($yssj_ysje));

                            } else {

                                $yssj_ysje = round($yssj_ysje, $qzfs['qzfs_qzws']);
                            }

                            $yssj_data[] = [
                                'scys_id'       => $data['scys_id'],
                                'yssj_fymc'     => $fybz_item['fybz_name'],
                                'fydy_id'       => $fybz_item['fydy_id'],
                                'yssj_cwyf'     => $calculate_item['cwyf'],
                                'yssj_kstime'   => $calculate_item['kstime'],
                                'yssj_jztime'   => $calculate_item['jztime'],
                                'yssj_fydj'     => $fybz_item['fybz_bzdj'],
                                'yssj_ysje'     => $yssj_ysje,
                                'fylx_id'       => 1,
                                'fybz_id'       => $fybz_item['fybz_id'],
                                'yssj_stuats'   => 0,
                                'yssj_fksj'     => '',
                                'shop_id'       => session('shop.shop_id'),
                                'xqgl_id'       => session('shop.xqgl_id'),
                                'member_id'     => $fyfp_fcxx_item['member_id'],
                                'syt_id'        => null,
                                'fcxx_id'       => $fyfp_fcxx_item['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                }
            }
            if (empty($yssj_data)) {
                continue;
            }

            $yssj = new Yssj();
            $yssj->saveAll($yssj_data);
        }

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

        return json(['status'=>200,'data'=>$data,'msg'=>'生成成功']);
    }

    function beforShopUpdate($data) {

        $louyu_id = Db::name('scys')->where('scys_id',$data['scys_id'])->value('louyu_id');

        if ($data['scys_scfs'] == 1) {

            $scys_ksyf_s = date('Y-m-01',$data['scys_ksyf']);
            $scys_jsyf_s = date('Y-m-01',$data['scys_jsyf']);

            $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($scys_ksyf_s) : ''; // 开始月份
            $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime("$scys_jsyf_s +1 month -1 day") : ''; // 终止月份

            if ($data['scys_ksyf'] > $data['scys_jsyf']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $scys_jsyf = Db::name('scys')
                ->where('scys_id','<',$data['scys_id'])
                ->where('scys_scgc',1)
                ->where('louyu_id',$louyu_id)
                ->order('scys_id','desc')->find();

            if (!empty($scys_jsyf)) {
                if ($data['scys_kstime'] != strtotime('+1 day', $scys_jsyf['scys_jsyf'])) {
                    return json([
                        'status'=>201,
                        'msg'=>"日期应从 ".date('Y-m-d',strtotime('+1 day', $scys_jsyf['scys_jsyf']))."开始"
                    ]);
                }
            }

        }

        if ($data['scys_scfs'] == 2) {

            $data['scys_kstime'] = !empty($data['scys_kstime']) ? strtotime($data['scys_kstime']) : ''; // 开始时间
            $data['scys_zztime'] = !empty($data['scys_zztime']) ? strtotime($data['scys_zztime']) : ''; // 终止时间

            if ($data['scys_kstime'] > $data['scys_zztime']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $scys_jsyf = Db::name('scys')
                ->where('scys_id','<',$data['scys_id'])
                ->where('scys_scgc',1)
                ->where('louyu_id',$louyu_id)
                ->order('scys_id','desc')->find();

            if (!empty($scys_jsyf)) {
                if ($data['scys_kstime'] != strtotime('+1 day', $scys_jsyf['scys_jsyf'])) {
                    return json([
                        'status'=>201,
                        'msg'=>"日期应从 ".date('Y-m-d',strtotime('+1 day', $scys_jsyf['scys_jsyf']))."开始"
                    ]);
                }
            }
        }

        $yssj = Db::name('yssj')
            ->where('scys_id',$data['scys_id'])
            ->where('yssj_stuats',1)
            ->select()->toArray();

        if (!empty($yssj)) {
            return json(['status'=>201,'msg'=>'已有缴费记录，不可重新生成']);
        }

        Db::name('yssj')->where('scys_id',$data['scys_id'])->delete();

    }

    function afterShopUpdate($data) {
        $data['louyu_id'] = Db::name('scys')->where('scys_id',$data['scys_id'])->value('louyu_id');
        return $this->afterShopAdd($data);
    }

    function afterShopIndex($data) {

        foreach ($data['data']['data'] as $data_key => $data_item) {

            $yssj_info = Db::name('yssj')->where('scys_id',$data_item['scys_id'])
                ->group('fcxx_id')
                ->select()->toArray();

            $fydy_id_arr = explode(',',$data_item['fydy_id']);
            $where1 = [];
            $where1[] = ['fydy_id','in',$fydy_id_arr];
            $fydy_name = Db::name('fydy')->where($where1)->column('fydy_name');
            $data['data']['data'][$data_key]['fydy']['fydy_name'] = implode('，',$fydy_name);

            if (count($yssj_info) == 1) {
                $fcxx_info = Db::name('fcxx')->alias('a')
                    ->leftJoin('louyu b','b.louyu_id=a.louyu_id')
                    ->where('a.fcxx_id',$yssj_info[0]['fcxx_id'])->find();

                $data['data']['data'][$data_key]['louyu_id'] = $fcxx_info['louyu_lyqz'].'-'.$fcxx_info['louyu_name'].'-'.$fcxx_info['fcxx_fjbh'];
            } else {
                $where = [];
                $where[] = ['louyu_id','=',$data_item['louyu_id']];
                $louyu_name = Db::name('louyu')->where($where)->column('louyu_name');

                if (empty($louyu_name)) {

                    $louyu_name = Db::name('louyu')->where([
                        'xqgl_id'=>session('shop.xqgl_id'),
                        'louyu_pid' => null
                    ])->column('louyu_name');

                }

                $data['data']['data'][$data_key]['louyu_id'] = implode(',',$louyu_name);
            }

        }

        return json($data);
    }

    function beforShopDelete($data) {

        $yssj = Db::name('yssj')
            ->where('scys_id',$data)
            ->where('yssj_stuats',1)
            ->select()->toArray();
        if (!empty($yssj)) {
            return json(['status'=>201,'msg'=>'已有缴费记录，不可删除']);
        }
    }

    function afterShopDelete($data) {

        $member_id_column = Db::name('yssj')->where('scys_id',$data)->column('member_id','member_id');

        Db::name('yssj')->where('scys_id',$data)->delete();

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

        $diff_member = array_diff_key($member_id_column,$member_yssj_ysje);

        foreach ($diff_member as $diff_member_key => $diff_member_item) {
            $member_yingshou[] = [
                'member_id' => $diff_member_key,
                'member_yingshou' => 0
            ];
        }

        $memberM = new \app\shop\model\Member();
        $memberM->saveAll($member_yingshou);

        return json(['status'=>200,'msg'=>'操作成功']);
    }

    function getMonthForDates($sDate, $eDate) {
        
        $sTime = strtotime(date('Y-m-01', strtotime($sDate)));
        $eTime = strtotime(date('Y-m-01', strtotime($eDate)));
        $months = [];
        while ($sTime <= $eTime) {
            $months[] = date('Y-m', $sTime);
            $sTime = strtotime('next month', $sTime);
        }
        return $months;
    }

    function calculateMonthScys($data) {

        $sDate = $data['scys_ksyf'];
        $eDate = $data['scys_jsyf'];

        $sTime = strtotime(date('Y-m-01', $sDate));
        $eTime = strtotime(date('Y-m-01', $eDate));
        $months = [];
        while ($sTime <= $eTime) {
            $jztime = date('Y-m-d', $sTime);
            $months[] =[
                'cwyf'      => date('Y-m', $sTime),
                'kstime'    => strtotime(date('Y-m-d', $sTime)),
                'jztime'    => strtotime("$jztime +1 month -1 day"),
            ];
            $sTime = strtotime('next month', $sTime);
        }
        return $months;
    }

    function calculateDaysScys($data,$fyfp_fcxx_item) {

        $sTime = strtotime(date('Y-m-01', $data['scys_kstime']));
        $eTime = strtotime(date('Y-m-01', $data['scys_zztime']));

        $month = [];
        while ($sTime <= $eTime) {
            $jztime = date('Y-m-d', $sTime);
            $month[] =[
                'cwyf'      => date('Y-m', $sTime),
                'kstime'    => strtotime(date('Y-m-d', $sTime)),
                'jztime'    => strtotime("$jztime +1 month -1 day"),
            ];
            $sTime = strtotime('next month', $sTime);
        }

        $month[0]['kstime'] = strtotime(date('Y-m-d', $data['scys_kstime']));
        $month[count($month)-1]['jztime'] =  $data['scys_zztime'];

        $months = $month;
        foreach ($month as $month_key => $month_item) {
            if ($month_key == 0) {
                // 计算残月 金额
                $months[$month_key]['ysje'] = $this->calculateMScys($data,$fyfp_fcxx_item,$month_item);
            } elseif ($month_key == count($month)-1) {
                $months[$month_key]['ysje'] = $this->calculateMScys($data,$fyfp_fcxx_item,$month_item);
            } else {
                $months[$month_key]['ysje'] = $fyfp_fcxx_item['fybz_bzdj'];
            }

        }

        return $months;
    }

    function calculateMScys($data,$fyfp_fcxx_item,$month_item) {
        $ysje = 0;
        $day = (($month_item['jztime'] - $month_item['kstime'])/86400)+1;

        if ($data['scys_sclx'] == 1) { // 30天
            $ysje = round (($fyfp_fcxx_item['fybz_bzdj'] / 30) * $day,2);
        }

        if ($data['scys_sclx'] == 2) { // 实际天数
            $cwyf = $month_item['cwyf'].'-01';
            $cwyf_s = strtotime($cwyf);
            $cwyf_e = strtotime("$cwyf +1 month -1 day");
            $d = (($cwyf_e - $cwyf_s)/86400)+1;
            $ysje = round (($fyfp_fcxx_item['fybz_bzdj'] / $d) * $day,2);
        }
        return $ysje;
    }


    function beforShopCheckboxAdd($data) {

        $fcxx_idx = explode(',',$data['fcxx_idx']);

        $louyu_id_column = Db::name('fcxx')->where('fcxx_id','in',$fcxx_idx)->column('louyu_id');

        $danyuan_id = Db::name('louyu')->where('louyu_id','in',$louyu_id_column)
            ->whereNotNull('louyu_pid')->column('louyu_pid');

        $louyu_id = Db::name('louyu')->where('louyu_id','in',$louyu_id_column)
            ->whereNull('louyu_pid')->column('louyu_id');

        $louyu_count = count(array_unique(array_merge($danyuan_id,$louyu_id)));

        if ($louyu_count > 1) {
            return json(['status'=>201,'msg'=>'请选择同一座楼里的房间']);
        }

        if ($data['scys_scfs'] == 1) {

            $scys_ksyf_s = date('Y-m-01',$data['scys_ksyf']);
            $scys_jsyf_s = date('Y-m-01',$data['scys_jsyf']);

            $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($scys_ksyf_s) : ''; // 开始月份
            $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime("$scys_jsyf_s +1 month -1 day") : ''; // 终止月份

            if ($data['scys_ksyf'] > $data['scys_jsyf']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $where_yssj = [];
            $where_yssj[] = ['fcxx_id','in',explode(',',$data['fcxx_idx'])];
            $where_yssj[] = ['yssj_kstime','>=',$data['scys_ksyf']];
            $where_yssj[] = ['yssj_jztime','<=',$data['scys_jsyf']];

            $yssj_info = Db::name('yssj')
                ->where($where_yssj)
                ->whereNotNull('scys_id')
                ->select()->toArray();

            if (!empty($yssj_info)) {
                return json([
                    'status'=>201,
                    'msg'=>date('Y-m-d',$data['scys_ksyf'])."~".date('Y-m-d',$data['scys_jsyf']).'之间存在生成的费用'
                ]);
            }
        }

        if ($data['scys_scfs'] == 2) {

            $data['scys_kstime'] = !empty($data['scys_kstime']) ? $data['scys_kstime'] : ''; // 开始时间
            $data['scys_zztime'] = !empty($data['scys_zztime']) ? $data['scys_zztime'] : ''; // 终止时间

            if ($data['scys_kstime'] > $data['scys_zztime']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $where_yssj = [];
            $where_yssj[] = ['fcxx_id','in',explode(',',$data['fcxx_idx'])];
            $where_yssj[] = ['yssj_kstime','>=',$data['scys_kstime']];
            $where_yssj[] = ['yssj_jztime','<=',$data['scys_zztime']];
            $where_yssj[] = ['scys_id','<>',null];

            $yssj_info = Db::name('yssj')
                ->where($where_yssj)
                ->whereNotNull('scys_id')
                ->select()->toArray();

            if (!empty($yssj_info)) {
                return json([
                    'status'=>201,
                    'msg'=>date('Y-m-d',$data['scys_kstime'])."~".date('Y-m-d',$data['scys_zztime']).'之间存在生成的费用'
                ]);
            }
        }

    }

    public function afterShopCheckboxAdd($data) {

        // [
        //  "shop_id" => 15
        //  "xqgl_id" => 35
        //  "scys_ksyf" => 1640966400
        //  "scys_jsyf" => 1672416000
        //  "scys_kstime" => ""
        //  "scys_zztime" => ""
        //  "jflx_id" => 2
        //  "louyu_id" => ""
        //  "fydy_id" => "79"
        //  "scys_scfs" => 1
        //  "scys_sclx" => 1
        //  "fcxx_idx" => "18309,18310,18311"
        //  "scys_scgc" => 1
        //  "scys_id" => 20230413
        // ]

        $fydy_id_arr = explode(',',$data['fydy_id']);

        // 获取费用标准
        $fybz = Db::name('fybz')
            ->field('fylx_id,fybz_id,fydy_id,fybz_name,fybz_bzdj')
            ->whereIn('fydy_id',$fydy_id_arr)
            ->select();

        $where_fyfp_fcxx = [];
        $where_fyfp_fcxx[] = ['b.fcxx_id','in',explode(',',$data['fcxx_idx'])];
        /*if (!empty($data['louyu_id'])) {
            $danyuan_ids = Db::name('louyu')->where('louyu_pid',$data['louyu_id'])->column('louyu_id');

            $danyuan_ids[] = $data['louyu_id'];

            $where_fyfp_fcxx[] = ['a.louyu_id','in',$danyuan_ids];
        }*/

        $member_id_arr = [];
        foreach ($fybz as $fybz_item) {

            $where_aa = [
                'c.fybz_id' => $fybz_item['fybz_id'],
                'b.shop_id' => session('shop.shop_id'),
                'b.xqgl_id' => session('shop.xqgl_id'),
            ];

            $fyfp_fcxx = Db::name('louyu')->alias('a')
                ->leftJoin('fcxx b','a.louyu_id=b.louyu_id')
                ->leftJoin('fyfp c','b.fcxx_id=c.fcxx_id')
                ->leftJoin('fybz d','c.fybz_id=d.fybz_id')
                ->field("c.*,b.fcxx_jzmj,b.member_id,d.fybz_bzdj,d.fybz_name,d.fybz_id,d.jfgs_id")
                ->where($where_aa)
                ->where($where_fyfp_fcxx)
                ->select()->toArray();

            $yssj_data = [];

            if (!empty($fyfp_fcxx)) {
                foreach ($fyfp_fcxx as $fyfp_fcxx_item) {

                    if ($fyfp_fcxx_item['member_id'] == 0) {
                        continue;
                    }

                    $member_id_arr[] = $fyfp_fcxx_item['member_id'];

                    $qzfs = Db::name('qzfs')->alias('a')
                        ->leftJoin('fydy b','a.qzfs_id= b.qzfs_id')
                        ->where('b.fydy_id',$fybz_item['fydy_id'])
                        ->find();

                    if ($data['scys_scfs'] == 1) { // 生成方式 按月生成

                        $yssj_cc = Db::name('yssj')
                            ->where('fcxx_id',$fyfp_fcxx_item['fcxx_id'])
                            ->where('fybz_id',$fybz_item['fybz_id'])
                            ->where('yssj_kstime','>=',$data['scys_ksyf'])
                            ->where('yssj_jztime','<=',$data['scys_jsyf'])->select()->toArray();

                        if (!empty($yssj_cc)) {
                            continue;
                        }

                        $calculate = $this->calculateMonthScys($data);

                        $yssj_ysje = 0;
                        if ($fyfp_fcxx_item['jfgs_id'] == 1) { // 1)单价

                            $yssj_ysje = $fyfp_fcxx_item['fybz_bzdj'];

                        } elseif ($fyfp_fcxx_item['jfgs_id'] == 2) { // 2)单价*使用面积

                            $yssj_ysje = $fyfp_fcxx_item['fybz_bzdj']*$fyfp_fcxx_item['fcxx_jzmj'];

                        }

                        if ($qzfs['qzfs_qzws'] == 0) {

                            $yssj_ysje = intval(round($yssj_ysje));

                        } else {

                            $yssj_ysje = round($yssj_ysje, $qzfs['qzfs_qzws']);

                        }


                        foreach ($calculate as $calculate_item) {

                            $yssj_data[] = [
                                'scys_id'       => $data['scys_id'],
                                'yssj_fymc'     => $fybz_item['fybz_name'],
                                'fydy_id'       => $fybz_item['fydy_id'],
                                'yssj_cwyf'     => $calculate_item['cwyf'],
                                'yssj_kstime'   => $calculate_item['kstime'],
                                'yssj_jztime'   => $calculate_item['jztime'],
                                'yssj_fydj'     => $fybz_item['fybz_bzdj'],
                                'yssj_ysje'     => $yssj_ysje,
                                'fylx_id'       => 1,
                                'fybz_id'       => $fybz_item['fybz_id'],
                                'yssj_stuats'   => 0,
                                'yssj_fksj'     => '',
                                'shop_id'       => session('shop.shop_id'),
                                'xqgl_id'       => session('shop.xqgl_id'),
                                'member_id'     => $fyfp_fcxx_item['member_id'],
                                'syt_id'        => null,
                                'fcxx_id'       => $fyfp_fcxx_item['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                    if ($data['scys_scfs'] == 2) { // 生成方式 按日生成


                        $yssj_cc = Db::name('yssj')
                            ->where('fcxx_id',$fyfp_fcxx_item['fcxx_id'])
                            ->where('fybz_id',$fybz_item['fybz_id'])
                            ->where('yssj_kstime','>=',$data['scys_kstime'])
                            ->where('yssj_jztime','<=',$data['scys_zztime'])->select()->toArray();

                        if (!empty($yssj_cc)) {
                            continue;
                        }

                        $calculate = $this->calculateDaysScys($data,$fyfp_fcxx_item);

                        foreach ($calculate as $calculate_item) {

                            $yssj_ysje = 0;

                            if ($fyfp_fcxx_item['jfgs_id'] == 1) { // 1)单价

                                $yssj_ysje = $calculate_item['ysje'];

                            } elseif ($fyfp_fcxx_item['jfgs_id'] == 2) { // 2)单价*使用面积

                                $yssj_ysje = $calculate_item['ysje']*$fyfp_fcxx_item['fcxx_jzmj'];
                            }

                            if ($qzfs['qzfs_qzws'] == 0) {

                                $yssj_ysje = intval(round($yssj_ysje));

                            } else {

                                $yssj_ysje = round($yssj_ysje, $qzfs['qzfs_qzws']);
                            }

                            $yssj_data[] = [
                                'scys_id'       => $data['scys_id'],
                                'yssj_fymc'     => $fybz_item['fybz_name'],
                                'fydy_id'       => $fybz_item['fydy_id'],
                                'yssj_cwyf'     => $calculate_item['cwyf'],
                                'yssj_kstime'   => $calculate_item['kstime'],
                                'yssj_jztime'   => $calculate_item['jztime'],
                                'yssj_fydj'     => $fybz_item['fybz_bzdj'],
                                'yssj_ysje'     => $yssj_ysje,
                                'fylx_id'       => 1,
                                'fybz_id'       => $fybz_item['fybz_id'],
                                'yssj_stuats'   => 0,
                                'yssj_fksj'     => '',
                                'shop_id'       => session('shop.shop_id'),
                                'xqgl_id'       => session('shop.xqgl_id'),
                                'member_id'     => $fyfp_fcxx_item['member_id'],
                                'syt_id'        => null,
                                'fcxx_id'       => $fyfp_fcxx_item['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                }
            }
            if (empty($yssj_data)) {
                continue;
            }

            $yssj = new Yssj();
            $yssj->saveAll($yssj_data);
        }

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

        return json(['status'=>200,'data'=>$data,'msg'=>'生成成功']);
    }

}