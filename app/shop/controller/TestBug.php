<?php
/*
 module:		收银台控制器
 create_time:	2022-12-31 10:06:14
 author:		
 contact:		
*/

namespace app\shop\controller;
use app\shop\model\Yssj;
use app\shop\model\Yssj as YssjModel;
use app\shop\model\Pjlx as PjlxModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\exception\ValidateException;
use app\shop\model\Syt as SytModel;
use app\shop\model\Cbgl as CbglModel;
use think\facade\Db;

class TestBug extends Admin {

    /*start*/
    /*
     * @Description  验证数据权限
     */
    function initialize(){
        parent::initialize();
        if(in_array($this->request->action(),['update','getUpdateInfo','delete'])){
            $idx = $this->request->post('yssj_id','','serach_in');
            if($idx){
                foreach(explode(',',$idx) as $v){
                    $info = SytModel::find($v);
                    if($info['xqgl_id'] <> session('shop.xqgl_id')){
                        throw new ValidateException('你没有操作权限');
                    }
                }
            }
        }
    }


    /*
     * @Description  数据列表
     */
    function index(){

        echo "E：What do you want to do? >>> 汉：汝欲何为？";
        exit;

        /*$where = [];
        $where[] = ['a.xqgl_id','=',session('shop.xqgl_id')];
        $where[] = ['a.fylx_id','=',3];

        $yssj = Db::name('yssj')->alias('a')
            ->field('a.member_id,sum(a.yssj_ysje) as yssj_ysje_a,a.member_id,a.member_id as fcxx_num,a.member_id as fcxx_id')
            ->withAttr('fcxx_num', function ($value, $data) {
                return Db::name('fcxx')->where('member_id',$value)->count();
            })
            ->withAttr('fcxx_id', function ($value, $data) {
                return Db::name('fcxx')->where('member_id',$value)->column('fcxx_id');
            })
            ->where($where)
            ->group('a.member_id')->select()->toArray();

        $_da = [];
        $_xiao = [];
        $_deng = [];

        foreach ($yssj as $yssj_item) {
            if ($yssj_item['yssj_ysje_a'] > 0) {
                $_da[] = [
                    'fcxx_id' => $yssj_item['fcxx_id'],
                    'member_id' => $yssj_item['member_id'],
                    'yssj_ysje_a' => $yssj_item['yssj_ysje_a'],
                    'fcxx_num' => $yssj_item['fcxx_num'],
                ];
            } elseif ($yssj_item['yssj_ysje_a'] < 0) {
                $_xiao[] = [
                    'fcxx_id' => $yssj_item['fcxx_id'],
                    'member_id' => $yssj_item['member_id'],
                    'yssj_ysje_a' => $yssj_item['yssj_ysje_a'],
                    'fcxx_num' => $yssj_item['fcxx_num'],
                ];
            } else {
                $_deng[] = [
                    'fcxx_id' => $yssj_item['fcxx_id'],
                    'member_id' => $yssj_item['member_id'],
                    'yssj_ysje_a' => $yssj_item['yssj_ysje_a'],
                    'fcxx_num' => $yssj_item['fcxx_num'],
                ];
            }
        }

        $_deng_y = [];

        foreach ($_da as $_da_item) {
            if ($_da_item['fcxx_num'] > 1) {
                $_da_y[] = $_da_item;
            }elseif ($_da_item['fcxx_num'] < 1) {
                $_xiao_y[] = $_da_item;
            } else {
                $_deng_y[] = $_da_item;
            }
        }

        $fcxx_insert = [];
        foreach ($_deng_y as $_deng_y_item) {
            $fcxx_insert[] = [
                'fcxx_id' => $_deng_y_item['fcxx_id'][0],
                'fcxx_yucun' => $_deng_y_item['yssj_ysje_a']
            ];
        }

        $FcxxM = new \app\shop\model\Fcxx();
        $FcxxM->saveAll($fcxx_insert);

        echo "完成";
        exit;*/

        /* // 计算应收费用
        $member_id = Db::name('member')->where('xqgl_id',session('shop.xqgl_id'))->column('member_id');

        $member_yingshou = [];

        foreach ($member_id as $member_id_item) {
            $yssj_ysje = Db::name('yssj')
                ->where('yssj_stuats',0)
                ->where('member_id',$member_id_item)
                ->sum('yssj_ysje');

            $member_yingshou[] = [
                'member_id' => $member_id_item,
                'member_yingshou' => $yssj_ysje,
            ];

        }

        $memberM = new \app\shop\model\Member();
        $memberM->saveAll($member_yingshou);*/





        /*$cbpc = Db::name('cbpc')->alias('a')
            ->field('a.cbpc_id as cbpcID,b.*')
            ->leftJoin('cbgl b','b.cbpc_id=a.cbpc_id')
            ->where('a.cbpc_id','in',[37,38])->select()->toArray();

        $yssj_data = [];
        foreach ($cbpc as $cbpc_item) {
            $yssj_id = Db::name('yssj')->where('cbgl_id',$cbpc_item['cbgl_id'])->value('yssj_id');
            $yssj_data[] = [
                'yssj_id'     => $yssj_id,
                'yssj_ysje'     => $cbpc_item['cbgl_cbje'],
            ];
        }

        $yssj = new Yssj();
        $yssj->saveAll($yssj_data);*/
//        dump($yssj_data);exit;

        /*$fydy_id = Db::name('fybz')->where('fybz_id',$cbgl['fybz_id'])->value('fydy_id');

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
        $yssj->saveAll($yssj_data);*/

        /*$cbgl_data = [];

        foreach ($cbpc as $cbpc_item) {

            $cbgl_sqsl = $cbpc_item['cbgl_sqsl']; // 上期数量

            $cbgl_bqsl = $cbpc_item['cbgl_bqsl']; // 本期数量

            $cbgl_shyl = $cbpc_item['cbgl_shyl']; // 损耗数量

            $cbgl_cbyl = $cbgl_bqsl - $cbgl_sqsl; // 抄表用量

            $cbgl_sjyl = $cbgl_bqsl - $cbgl_sqsl - $cbgl_shyl; // 实际用量

            $qzfs = Db::name('fybz')->alias('a')
                ->field('c.*')
                ->leftJoin('fydy b','a.fydy_id=b.fydy_id')
                ->leftJoin('qzfs c','b.qzfs_id=c.qzfs_id')
                ->where('fybz_id',$cbpc_item['fybz_id'])
                ->find();

            $cbgl_cbje = $cbgl_sjyl * $cbpc_item['cbgl_ybbl'] * $cbpc_item['fybz_bzdj'];

            if ($qzfs['qzfs_qzws'] == 0) {
                $cbgl_cbje = intval(round($cbgl_cbje));
            } else {
                $cbgl_cbje = round($cbgl_cbje, $qzfs['qzfs_qzws']);
            }

            $cbgl_data[] = [

                'cbgl_id'       => $cbpc_item['cbgl_id'],

                'cbgl_cbyl'     => $cbgl_cbyl, // 本期数量 - 上期数量

                'cbgl_sjyl'     => $cbgl_sjyl, // 本期数量 - 上期数量 - 损耗用量

                'cbgl_cbje'     => $cbgl_cbje,

            ];

        }

        $cbgl = new CbglModel();
        $cbgl->saveAll($cbgl_data);*/

        echo "完成";
    }
	/*end*/
}

