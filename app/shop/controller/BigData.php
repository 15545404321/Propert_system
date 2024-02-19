<?php 
/*
 module:		大数据看板控制器
 create_time:	2023-01-30 16:33:31
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\BigData as BigDataModel;
use think\facade\Db;

class BigData extends Admin {

    function index(){
//        [
//          "shop_admin_id" => 1,
//          "shop_id" => 1,
//          "root" => "1",
//          "cname" => "九福物业",
//          "password" => "656ea9ad41057cf9eca57da6451b61db",
//          "create_time" => 1670327210,
//          "update_time" => 1674198194,
//          "disable" => 1,
//          "account" => "jiufu",
//          "tel" => null,
//          "zzgl_id" => null,
//          "gwgl_id" => null,
//          "xqgl_ids" => null,
//          "xqgl_id" => 9,
//          "role_id" => "1",
//          "username" => "jiufu",
//          "xqgl_name" => "九福一小区"
//        ];

        $where=[];
        $where[] =['xqgl_id','=',session("shop.xqgl_id")];
        $where[] =['shop_id','=',session("shop.shop_id")];


        $whereThisYear[] =['yssj_kstime','>=',strtotime(date('Y-01-01',time()))];
        $whereThisYear[] =['yssj_jztime','<=',strtotime(date('Y-12-31',time()))];

        $cewei_jiaofei = Db::name('yssj')
            ->where($where)
            ->where('yssj_stuats',1)
            ->where('cewei_id is not Null')
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $cewei_daijaio = Db::name('yssj')
            ->where($where)
            ->where('yssj_stuats',0)
            ->where('cewei_id is not Null')
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $cewei_total = Db::name('yssj')
            ->where($where)
            ->where('cewei_id is not Null')
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $cewei_clip = round($cewei_jiaofei/$cewei_total*100,2);




        $shoufei_yssj = Db::name('yssj')
            ->where($where)
            ->where('yssj_stuats',1)
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $shoufei_total = Db::name('yssj')
            ->where($where)
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $shoufei_clip = round($shoufei_yssj/$shoufei_total*100,2);



        $whereBeforeYear = [];
        $whereThisYear[] =['yssj_kstime','<',strtotime(date('Y-01-01',time()))];


        $chenqianheje = Db::name('yssj')
            ->where($where)
            ->where('yssj_stuats',1)
            ->where($whereBeforeYear)
            ->sum('yssj_ysje');
        $chenqianzongje = Db::name('yssj')
            ->where($where)
            ->where($whereBeforeYear)
            ->sum('yssj_ysje');
        $qingqian_clip = round($chenqianheje/$chenqianzongje*100,2);



        $yssj_main1 = Db::name('fydy')->alias('a')
            ->field('a.fydy_id,a.fydy_name,sum(c.yssj_ysje) as yssj_ysje')
            ->leftJoin('fybz b','a.fydy_id=b.fydy_id')
            ->leftJoin('yssj c','c.fybz_id=b.fybz_id')
            ->where('a.xqgl_id',session("shop.xqgl_id"))
            ->where('c.yssj_kstime','>',strtotime(date('Y-01-01',time())))
            ->where('c.yssj_jztime','<=',strtotime(date('Y-12-31',time())))
            ->where('a.fylx_id','<>',3)
            ->group('a.fydy_id')
            ->select()
            ->toArray();
        $yssj_ysk = Db::name('yssj')
            ->where('xqgl_id',session("shop.xqgl_id"))
            ->where('fylx_id','=',3)
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $main1 = [];
        foreach ($yssj_main1 as $yssj_main1_key => $yssj_main1_time) {
            if (empty($yssj_main1_time['yssj_ysje'])) $yssj_main1_time['yssj_ysje'] = 0;
            $main1[$yssj_main1_key]['name'] = $yssj_main1_time['fydy_name'];
            $main1[$yssj_main1_key]['value'] = round($yssj_main1_time['yssj_ysje'],2);
        }
        $main1[] = ['name' => '预收款','value' => round($yssj_ysk,2)];



        $yssj_main2 = Db::name('fydy')->alias('a')
            ->field('a.fydy_id,a.fydy_name,sum(c.yssj_ysje) as yssj_ysje')
            ->leftJoin('fybz b','a.fydy_id=b.fydy_id')
            ->leftJoin('yssj c','c.fybz_id=b.fybz_id')
            ->where('a.xqgl_id',session("shop.xqgl_id"))
            ->where('a.fylx_id','<>',3)
            ->group('a.fydy_id')
            ->select()
            ->toArray();
        $yssj_ysk = Db::name('yssj')
            ->where('xqgl_id',session("shop.xqgl_id"))
            ->where('fylx_id','=',3)
            ->where($whereThisYear)
            ->sum('yssj_ysje');
        $main2 = [];
        foreach ($yssj_main2 as $yssj_main2_key => $yssj_main2_time) {
            if (empty($yssj_main2_time['yssj_ysje'])) {
                $main2[$yssj_main2_key]['value'] = 0.00;
            } else {
                $main2[$yssj_main2_key]['value'] = round($yssj_main2_time['yssj_ysje'],2);
            }
            $main2[$yssj_main2_key]['name'] = $yssj_main2_time['fydy_name'];
        }
        $main2[] = ['name' => '预收款','value' => round($yssj_ysk,2)];



        $syt_main3 = Db::name('syt')->alias('a')
            ->field('sum(a.syt_skje) as syt_skje,b.skfs_id')
            ->leftJoin('skfs b','a.syt_method=b.skfs_id')
            ->where('a.xqgl_id',session("shop.xqgl_id"))
            ->where('a.syt_method','<>',0)
            ->where('a.syt_zfsj','>',strtotime(date('Y-01-01',time())))
            ->where('a.syt_zfsj','<=',strtotime(date('Y-12-31',time())))
            ->group('b.skfs_id')
            ->order('b.skfs_id','asc')
            ->select();
        $main3series = [];       $main3xAxis = [];      $inarr = [];
        $skfs = Db::name('skfs')->column('skfs_name','skfs_id');
        foreach ($skfs as $skfs_key => $skfs_item) {
            foreach ($syt_main3 as $syt_main3_item) {
                if ($syt_main3_item['skfs_id'] == $skfs_key) {
                    $main3xAxis[] = $skfs_item;
                    $main3series[] = round($syt_main3_item['syt_skje'],2);
                    $inarr[$syt_main3_item['skfs_id']] = $syt_main3_item['skfs_id'];
                }
            }
        }
        $uinarr = array_diff_key($skfs,$inarr);
        foreach ($uinarr as $uinarr_key => $uinarr_item) {
            $main3xAxis[] = $uinarr_item;
            $main3series[] = 0.00;
        }



        $chuli = Db::name('bxxx')->field("bxxx_time")->where($where)
            ->where('bxxx_time','>',strtotime(date('Y-01-01',time())))
            ->where('bxxx_time','<=',strtotime(date('Y-12-31',time())))
            ->where('bxxx_start','<>',2)
            ->select()->toArray();
        $baoxiu = Db::name('bxxx')->field("bxxx_time")->where($where)
            ->where('bxxx_time','>',strtotime(date('Y-01-01',time())))
            ->where('bxxx_time','<=',strtotime(date('Y-12-31',time())))
            ->where('bxxx_start','=',2)
            ->select()->toArray();
        $main4chuli = $this->MonthDateNum($chuli);
        $main4baoxiu = $this->MonthDateNum($baoxiu);



        $xttz = Db::name('wzgl')->field("wzgl_time")->where($where)
            ->where('wzgl_time','>',strtotime(date('Y-01-01',time())))
            ->where('wzgl_time','<=',strtotime(date('Y-12-31',time())))
            ->select()->toArray();
        $fwtz = Db::name('bxxx')->field("bxxx_time")->where($where)
            ->where('bxxx_time','>',strtotime(date('Y-01-01',time())))
            ->where('bxxx_time','<=',strtotime(date('Y-12-31',time())))
            ->select()->toArray();
        $main5xttz = $this->MonthDateNum($xttz);
        $main5fwtz = $this->MonthDateNum($fwtz);


        return view('index',[
            // 基本信息 start
            'shop_name' => session('shop.cname'),
            'xqgl_name' => session('shop.xqgl_name'),
            // 基本信息 end

            // 表1 start
            'louyu_num' => Db::name('louyu')->where($where)->whereNull('louyu_pid')->count(), // 楼座数量
            'louyu_kzl' => 2.67, // TODO 空置率
            'louyu_zmj' => Db::name('fcxx')->where($where)->sum('fcxx_jzmj'), // 总面积

            'fcxx_num' => Db::name('fcxx')->where($where)->count(), // 房间数
            'fcxx_chu' => Db::name('fcxx')->where($where)->where('member_id','<>',0)->count(), // 已入住
            'fcxx_kong' => Db::name('fcxx')->where($where)->where('member_id','=',0)->count(),// 空置

            'cewei_num' => Db::name('cewei')->where($where)->count(), // 车位数
            'cewei_chu' => Db::name('cewei')->where($where)->where('member_id is not Null')->count(), // 已出售
            'cewei_kong' => Db::name('cewei')->where($where)->where('member_id is Null')->count(), // 空置

            'car_num' => Db::name('car')->where($where)->count(), // 车辆数
            'car_gudi' => Db::name('car')->where($where)->where('car_type','=',1)->count(), // 固定车辆
            'car_linshi' => Db::name('car')->where($where)->where('car_type','=',2)->count(), // 临时车辆

            'rzl' => 43, // TODO 认证
            'member_rzl' => 2,
            'fcxx_rzl' => 2,

            'cewei_clip' => $cewei_clip, // 车位收费率
            'cewei_jiaofei' => $cewei_jiaofei/10000, // 当前已收金额
            'cewei_daijaio' => $cewei_daijaio/10000, // 当前应收金额
            'cewei_clip_num' => 110-$cewei_clip, // 图形

            'shoufei_clip' => $shoufei_clip, // 收费率
            'shoufei_clip_num' => 110-$shoufei_clip, // 图形

            'qingqian_clip' => $qingqian_clip, // 清欠率
            'chenqianheje' => $chenqianheje/10000, // 陈欠核金额
            'chenqianzongje' => $chenqianzongje/10000, // 陈欠总金额
            'qingqian_clip_num' => 110-$qingqian_clip, // 图形
            // 表1 end

            // 表2 start 欠费展示
            'main1' => json_encode($main1, JSON_UNESCAPED_UNICODE),
            // 表2 end

            // 表3 start
            'main2' => json_encode($main2, JSON_UNESCAPED_UNICODE),
            // 表3 end

            // 表4 start
            'main3' => [
                'xAxis' => json_encode($main3xAxis,JSON_UNESCAPED_UNICODE),
                'series' => json_encode($main3series,JSON_UNESCAPED_UNICODE),
            ],
            // 表4 end

            // 表5 start
            'main4' => [
                'baoxiu' => json_encode($main4baoxiu,JSON_UNESCAPED_UNICODE),
                'chuli' => json_encode($main4chuli,JSON_UNESCAPED_UNICODE),
            ],
            // 表5 end

            // 表6 start
            'main5' => [
                'xttz' => json_encode($main5xttz,JSON_UNESCAPED_UNICODE),
                'fwtz' => json_encode($main5fwtz,JSON_UNESCAPED_UNICODE),
            ],
            // 表6 end
        ]);
    }

    function MonthDateNum($data) {
        if (empty($data)) {return [];}
        $January = $February = $March = $April = $May = $June = $July = $August = $September = $October = $November = $December = 0;
        foreach ($data as $data_item) {
            $time = 0;
            if (isset($data_item['bxxx_time']) && !empty($data_item['bxxx_time'])) {
                $time = $data_item['bxxx_time'];
            }
            if (isset($data_item['wzgl_time']) && empty($data_item['wzgl_time'])) {
                $time = $data_item['bxxx_time'];
            }
            switch (date('m',$time)) {
                case '01':$January++;break;case '02':$February++;break;
                case '03':$March++;break;case '04':$April++;break;
                case '05':$May++;break;case '06':$June++;break;
                case '07':$July++;break;case '08':$August++;break;
                case '09':$September++;break;case '10':$October++;break;
                case '11':$November++;break;case '12':$December++;break;
            }
        }
        return [$January,$February,$March,$April,$May,$June,$July,$August,$September,$October,$November,$December];
    }

}

