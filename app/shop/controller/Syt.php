<?php
/*
 module:		收银台控制器
 create_time:	2022-12-31 10:06:14
 author:		
 contact:		
*/

namespace app\shop\controller;
use app\shop\model\Yssj as YssjModel;
use app\shop\model\Pjlx as PjlxModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\exception\ValidateException;
use app\shop\model\Syt as SytModel;
use think\facade\Db;

class Syt extends Admin {

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

    function indexCheck() {
        $fcxx_id = $this->request->post('fcxx_id', '', 'serach_in');
        $cewei_id = $this->request->post('cewei_id', '', 'serach_in');
        $member_id = $this->request->post('member_id', '', 'serach_in');
        $where['yssj.shop_id'] = session('shop.shop_id');
        $where['yssj.xqgl_id'] = session('shop.xqgl_id');

        if (empty($member_id)) {

            if (!empty($fcxx_id) || !empty($cewei_id)) {

                $pay_member_id = 0;

                if (!empty($fcxx_id)) {
                    $pay_member_id = Db::name('fcxx')
                        ->where('fcxx_id',$fcxx_id)->value('member_id');
                }

                if (!empty($cewei_id)) {
                    $pay_member_id = Db::name('cewei')
                        ->where('cewei_id',$cewei_id)->value('member_id');
                }

                if (!empty($member_id)) {
                    $pay_member_id = intval($member_id);
                }

                $member_idx = Db::name('member')->where('member_id',$pay_member_id)->value('member_idx');
                $member_ids = explode(',',$member_idx);
                $member_ids[] = $pay_member_id;

                $member_ids = array_filter($member_ids);

                $fcxx_count = Db::name('fcxx')->where('member_id','in',$member_ids)->count();
                $cewei_count = Db::name('cewei')->where('member_id','in',$member_ids)->count();

                $yssj_count = Db::name('yssj')
                    ->where('yssj_stuats',0)
                    ->where('fylx_id',3)
                    ->where('member_id',$pay_member_id)->count();

                $fcxx_cewei_count = $fcxx_count + $cewei_count + $yssj_count;

                if ($fcxx_cewei_count > 1) {
                    $data['status'] = 201;
                    return json($data);
                }

            }
        }

        $data['status'] = 200;
        return json($data);
    }

    /*
     * @Description  数据列表
     */
    function index(){
        if (!$this->request->isPost()){

            return view('index');
        }else{

            $limit  = $this->request->post('limit', 1000, 'intval');
            $page = $this->request->post('page', 1, 'intval');			
            $where = [];
			/////////按fybz_id筛选//////////
			$fybzs = $this->request->post('fybz_id', '', 'serach_in');
			$related = $this->request->post('related', '', 'serach_in');
			if (!empty($fybzs)){
				$fybz_ids_arr = explode(',',$fybzs);
				$where['yssj.fybz_id'] = array('in',$fybz_ids_arr);
			}
			/////////按fybz_id筛选//////////
            $where['yssj_id'] = $this->request->post('yssj_id', '', 'serach_in');
            $where['yssj.fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
			//////////////车位/////////////////////////
            $where['yssj.cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
			//////////////车位/////////////////////////

            $where['yssj.shop_id'] = session('shop.shop_id');

            $where['yssj.xqgl_id'] = session('shop.xqgl_id');
            $where['yssj.member_id'] = $this->request->post('member_id', '', 'serach_in');
            $where['yssj.yssj_stuats'] = 0;

            $pay_member_yucun = 0;
            if (!empty($where['yssj.fcxx_id'])) {
                $pay_member_yucun = Db::name('fcxx')->where('fcxx_id',$where['yssj.fcxx_id'])
                    ->value('fcxx_yucun');
            }

            $field = 'yssj_id,yssj_fymc,yssj_cwyf,yssj_kstime,yssj_jztime,yssj_fydj,yssj_ysje,sjlx_id';

            $withJoin = [
                'fylx'=>explode(',','fylx_name'),
                'fybz'=>explode(',','fybz_name'),
                'fcxx'=>explode(',','fcxx_fjbh,fcxx_id,louyu_id'),
                'member'=>explode(',','member_id,member_name'),
                'cewei'=>explode(',','cewei_id,cewei_name,tccd_id,cwqy_id'),
            ];

            $order  = $this->request->post('order', '', 'serach_in');	//排序字段
            $sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

            $orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

            $query = YssjModel::field($field);

            $pay_member_id = 0;
            $check_fcxx_id = 0;

            $res = [];

            $res[0]['fcxx'][] = [
                'table'         => [],
                'title'         => '房间资产',
                'tableIndex'    => 0
            ];

            $res[0]['cewei'][] = [
                'table'         => [],
                'title'         => '车位资产',
                'tableIndex'    => 1
            ];

                //************* START *************//
            if (!empty($where['yssj.fcxx_id']) || !empty($where['yssj.member_id']) || !empty($where['yssj.cewei_id'])) {

                $res = [];

                if (!empty($where['yssj.fcxx_id'])) {
                    $check_fcxx_id = $where['yssj.fcxx_id'];
                    $pay_member_id = Db::name('fcxx')
                        ->where('fcxx_id',$where['yssj.fcxx_id'])->value('member_id');
                }
                //////////////爽加/////////////////////////
                if (!empty($where['yssj.cewei_id'])) {
                    $pay_member_id = Db::name('cewei')
                        ->where('cewei_id',$where['yssj.cewei_id'])->value('member_id');
                }
                //////////////爽加/////////////////////////
                if (!empty($where['yssj.member_id'])) {
                    $pay_member_id = intval($where['yssj.member_id']);
                }

                if ($related == 1) { // 显示所有关联

                    if (!empty($where['yssj.fcxx_id'])) {
                        unset($where['yssj.fcxx_id']);
                    }
                    //////////////爽加/////////////////////////
                    if (!empty($where['yssj.cewei_id'])) {
                        unset($where['yssj.cewei_id']);
                    }
                    //////////////爽加/////////////////////////
                    if (!empty($where['yssj.member_id'])) {
                        unset($where['yssj.member_id']);
                    }

                    $member_idx = Db::name('member')->where('member_id',$pay_member_id)->value('member_idx');

                    if (!empty($member_idx)) {
                        $member_idx_arr = explode(',',$member_idx);
                    }

                    $member_idx_arr[] = $pay_member_id;

                    $where['yssj.member_id'] = ['in',$member_idx_arr];

                }

                $table = $query->where(formatWhere($where))
                    ->withJoin($withJoin,'left')->order($orderby)->select()->toArray();

                $ta1 = [];

                foreach ($table as $table_item) {
                    if ($table_item['sjlx_id'] == 1) {
                        $ta1[$table_item['member']['member_id']]['fcxx'][$table_item['fcxx']['fcxx_id']][] = $table_item;
                    }
                    if ($table_item['sjlx_id'] == 2) {
                        $ta1[$table_item['member']['member_id']]['cewei'][$table_item['cewei']['cewei_id']][] = $table_item;
                    }
                }

                $tableIndex = 0;

                foreach ($ta1 as $ta1_key => $ta1_item) {

                    $member_name = Db::name('member')
                        ->where('member_id',$ta1_key)->value('member_name');

                    $i = 0;
                    if (!empty($ta1_item['fcxx'])) {

                        foreach ($ta1_item['fcxx'] as $ta1_fcxx) {
                            $tableIndex++;

                            $danyaun = Db::name('louyu')->field('louyu_pid,louyu_name')
                                ->where('louyu_id',$ta1_fcxx[0]['fcxx']['louyu_id'])->find();

                            $louyu_name = Db::name('louyu')->field('louyu_pid,louyu_name')
                                ->where('louyu_id',$danyaun['louyu_pid'])->value('louyu_name');

                            if ($ta1_key == $pay_member_id) {
                                if (empty($ta1_fcxx[0]['fcxx']['fcxx_id'])) {
                                    $res[$i]['fcxx'][] = [
                                        'table'         => $ta1_fcxx,
                                        'title'         => $member_name.'-预收类费-应收费用',
                                        'tableIndex'    => $tableIndex
                                    ];
                                } else {
                                    $res[$i]['fcxx'][] = [
                                        'table'         => $ta1_fcxx,
                                        'title'         => $member_name.'-'.$louyu_name.'-'.$danyaun['louyu_name'].'['.$ta1_fcxx[0]['fcxx']['fcxx_fjbh'].']-房间资产',
                                        'tableIndex'    => $tableIndex
                                    ];
                                }
                            } else {
                                $i++;
                                if (empty($ta1_fcxx[0]['fcxx']['fcxx_id'])) {
                                    $res[$i]['fcxx'][] = [
                                        'table'         => $ta1_fcxx,
                                        'title'         => $member_name.'-预收类费-应收费用',
                                        'tableIndex'    => $tableIndex
                                    ];
                                } else {
                                    $res[$i]['fcxx'][] = [
                                        'table'         => $ta1_fcxx,
                                        'title'         => $member_name.'-'.$louyu_name.'-'.$danyaun['louyu_name'].'['.$ta1_fcxx[0]['fcxx']['fcxx_fjbh'].']-房间资产',
                                        'tableIndex'    => $tableIndex
                                    ];
                                }
                            }
                        }
                    }
                    if (!empty($ta1_item['cewei'])) {

                        foreach ($ta1_item['cewei'] as $ta1_cewei) {
                            $tableIndex++;

                            $tccd_name = Db::name('tccd')
                                ->where('tccd_id',$ta1_cewei[0]['cewei']['tccd_id'])->value('tccd_name');

                            $cwqy_name = Db::name('cwqy')
                                ->where('cwqy_id',$ta1_cewei[0]['cewei']['cwqy_id'])->value('cwqy_name');

                            if ($ta1_key == $pay_member_id) {
                                $res[$i]['cewei'][] = [
                                    'table'         => $ta1_cewei,
                                    'title'         => $member_name.'-'.$tccd_name.'-'.$cwqy_name.'['.$ta1_cewei[0]['cewei']['cewei_name'].']-车位资产',
                                    'tableIndex'    => $tableIndex
                                ];
                            } else {
                                $i++;
                                $res[$i]['cewei'][] = [
                                    'table'         => $ta1_cewei,
                                    'title'         => $member_name.'-'.$tccd_name.'-'.$cwqy_name.'['.$ta1_cewei[0]['cewei']['cewei_name'].']-车位资产',
                                    'tableIndex'    => $tableIndex
                                ];
                            }
                        }
                    }
                }
                //************* END *************//
            }

//            $pay_member_yucun = Db::name('member')->where('member_id',$pay_member_id)->value('member_yucun');

            if (empty($pay_member_yucun)) $pay_member_yucun = 0;

            $data['status'] = 200;
            $data['data']['data'] = $res;
            $data['pay_member_id'] = $pay_member_id;
            $data['check_fcxx_id'] = $check_fcxx_id;
            $data['pay_member_yucun'] = $pay_member_yucun;
            $page == 1 && $data['sql_field_data'] = $this->getSqlField('fcxx_id,fylx_id,fybz_id,member_id');
            return json($data);
        }
    }

    function indexLists(){
        if (!$this->request->isPost()){
            return view('indexLists');
        }else{
            $limit  = $this->request->post('limit', 20, 'intval');
            $page = $this->request->post('page', 1, 'intval');

            $where = [];
            $where['syt_id'] = $this->request->post('syt_id', '', 'serach_in');
            $where['fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
            $where['cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
            $where['member_id'] = $this->request->post('member_id', '', 'serach_in');

            $cewei_info = Db::name('cewei')->where('cewei_id',$where['cewei_id'])->find();
            if (!empty($where['cewei_id']) && empty($where['member_id'])) {
                $where['member_id'] = $cewei_info['member_id'];
                unset($where['cewei_id']);
            }

            $field = 'syt_id,syt_method,syt_invoice,syt_skje,syt_zfsj,syt_zlje,syt_bz,member_id,syt_dcje';

            $order  = $this->request->post('order', '', 'serach_in');	//排序字段
            $sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

            $orderby = ($sort && $order) ? $sort.' '.$order : 'syt_id desc';

            $query = SytModel::field($field);

            $res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();
            foreach($res['data'] as $k=>$v){
                if($v['member_id']){
                    $res['data'][$k]['member_id'] = Db::query("select member_name from  cd_member where member_id=".$v['member_id']."")[0]['member_name'];
                }
            }
            $data['status'] = 200;
            $data['data'] = $res;
            return json($data);
        }
    }

    /*
    * @Description  获取远程搜索房产字段信息
    */
    public function remoteFcxxidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "fcxx_fjbh like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'fcxx_id = '.$dataval;
        }
        $data = $this->query("select fcxx_id as tval,concat_ws('_',b.louyu_lyqz,b.louyu_name,a.fcxx_fjbh) as tkey from cd_fcxx a left join cd_louyu b on a.louyu_id=b.louyu_id where a.xqgl_id=".session('shop.xqgl_id')." and a.shop_id=".session('shop.shop_id')." and ".$sqlstr,'mysql');
        return json(['status'=>200,'data'=>$data]);
    }

    /*
    * @Description  获取远程搜索费用标准字段信息
    */
    public function remoteFybzidList(){
        /*$queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
		$sqlstr = '1';
        if($queryString){
            $sqlstr = "fybz_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'fybz_id = '.$dataval;
        }*/
        $data = $this->query("select fybz_id,fybz_name from cd_fybz where shop_id=".session("shop.shop_id")." and xqgl_id=".session('shop.xqgl_id')." order by fylx_id asc",'mysql');
		
        return json(['status'=>200,'data'=>$data]);
    }
	
	
	
    /*
    * @Description  获取远程搜索车位字段信息
    */
    public function remoteCeweiidList(){
		
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "cewei_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'cewei_id = '.$dataval;
        }
        $data = $this->query("select cewei_id as tval,concat_ws('-',b.tccd_name,e.cwqy_name,a.cewei_name) as tkey from cd_cewei a 
		left join cd_tccd b on a.tccd_id=b.tccd_id 
		left join cd_cwqy e on a.cwqy_id=e.cwqy_id 
		where a.xqgl_id=".session('shop.xqgl_id')." and a.shop_id=".session('shop.shop_id')." and ".$sqlstr,'mysql');

        return json(['status'=>200,'data'=>$data]);
    }


    /*
    * @Description  获取远程搜索车位字段信息
    */
    public function remoteCeweiidListArray(){

        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        $queryArray = explode(',',$queryString);
        if($queryString){
            $sqlstr = "cewei_name like '".$queryArray[0]."%' and member_id=".$queryArray[1];
        }
        if($dataval){
            $sqlstr = 'cewei_id = '.$dataval;
        }
        $data = $this->query("select cewei_id as tval,concat_ws('-',b.tccd_name,e.cwqy_name,a.cewei_name) as tkey from cd_cewei a 
		left join cd_tccd b on a.tccd_id=b.tccd_id 
		left join cd_cwqy e on a.cwqy_id=e.cwqy_id 
		where a.xqgl_id=".session('shop.xqgl_id')." and a.shop_id=".session('shop.shop_id')." and ".$sqlstr,'mysql');

        return json(['status'=>200,'data'=>$data]);
    }

    /*
    * @Description  获取远程搜索字段信息
    */
    public function remoteMemberidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "member_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'member_id = '.$dataval;
        }
        $data = $this->query("select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")." and ".$sqlstr,'mysql');
        return json(['status'=>200,'data'=>$data]);
    }


    /*
     * @Description  修改排序开关
     */
    function updateExt(){
        $postField = 'yssj_id,';
        $data = $this->request->only(explode(',',$postField),'post',null);
        if(!$data['yssj_id']) throw new ValidateException ('参数错误');
        SytModel::update($data);
        return json(['status'=>200,'msg'=>'操作成功']);
    }

    /*
     * @Description  添加
     */
    public function add(){
        $postField = 'fcxx_id,syt_method,zkgl_id,syt_invoice,syt_skje,syt_zfsj,syt_zlje,syt_bz,shop_id,xqgl_id,member_id';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $this->validate($data,\app\shop\validate\Syt::class);

        $data['syt_zfsj'] = time();
        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');

        try{
            $res = SytModel::insertGetId($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }
        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
    }


    /*
     * @Description  修改
     */
    public function update(){
        $postField = 'yssj_id,fcxx_id,syt_method,zkgl_id,syt_invoice,syt_skje,syt_zfsj,syt_zlje,syt_bz,shop_id,xqgl_id,member_id';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $this->validate($data,\app\shop\validate\Syt::class);


        if(!isset($data['fcxx_id'])){
            $data['fcxx_id'] = null;
        }
        $data['syt_zfsj'] = !empty($data['syt_zfsj']) ? strtotime($data['syt_zfsj']) : '';
        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');

        try{
            SytModel::update($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }
        return json(['status'=>200,'msg'=>'修改成功']);
    }


    /*
     * @Description  修改信息之前查询信息的 勿要删除
     */
    function getUpdateInfo(){
        $id =  $this->request->post('yssj_id', '', 'serach_in');
        if(!$id) throw new ValidateException ('参数错误');
        $field = 'yssj_id,fcxx_id,syt_method,zkgl_id,syt_invoice,syt_skje,syt_zfsj,syt_zlje,syt_bz,shop_id,xqgl_id,member_id';
        $res = SytModel::field($field)->find($id);
        return json(['status'=>200,'data'=>$res]);
    }


    /*
     * @Description  删除
     */
    function delete(){
        $idx =  $this->request->post('yssj_id', '', 'serach_in');
        if(!$idx) throw new ValidateException ('参数错误');
        SytModel::destroy(['yssj_id'=>explode(',',$idx)],true);
        return json(['status'=>200,'msg'=>'操作成功']);
    }


    /*
     * @Description  获取定义sql语句的字段信息
     */
    function getFieldList(){
        return json(['status'=>200,'data'=>$this->getSqlField('zkgl_id')]);
    }

    /*
     * @Description  获取定义sql语句的字段信息
     */
    function getFydy_id(){
        return json(['status'=>200,'data'=>$this->getSqlField('fydy_id')]);
    }

    /*
     * @Description  获取定义sql语句的字段信息
     */
    private function getSqlField($list){
        $data = [];
        /*if(in_array('fcxx_id',explode(',',$list))){
            $data['fcxx_ids'] = $this->query("select fcxx_id,fcxx_fjbh from cd_fcxx where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",'mysql');
        }*/
        if(in_array('fylx_id',explode(',',$list))){
            $data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
        }
        if(in_array('fybz_id',explode(',',$list))){
            $data['fybz_ids'] = $this->query("select fybz_id,fybz_name from cd_fybz",'mysql');
        }
        if(in_array('fydy_id',explode(',',$list))){
            $data['fydy_ids'] = $this->query("select fydy_id,fydy_name from cd_fydy where xqgl_id=".session("shop.xqgl_id")." and fylb_id=5 and fylx_id=1",'mysql');
        }
        /*if(in_array('member_id',explode(',',$list))){
            $data['member_ids'] = $this->query("select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",'mysql');
        }*/
        if(in_array('skfs_id',explode(',',$list))){
            $data['skfs_ids'] = $this->query("select skfs_id,skfs_name from cd_skfs",'mysql');
        }
        return $data;
    }

    function getFormInfo() {
        return json(['status'=>200,'data'=>$this->getSqlField('skfs_id')]);
    }

    function submitSk() {

        $data = $this->request->post();

        if (empty($data['yssj_ids'])) { // 勾选缴费

            return json(['status'=>201,'data'=>$data, 'msg'=>'请勾选缴费项']);

        }

        if (empty($data['pay_member_id'])) { // 缴费人

            return json(['status'=>201,'data'=>$data, 'msg'=>'请勾选缴费人']);

        }

        if (empty($data['skfs_id'])) { // 收款方式

            return json(['status'=>201,'data'=>$data, 'msg'=>'请选择收款方式']);

        }

        if (empty($data['syt_yskcd'])) { // 预收款冲抵

            return json(['status'=>201,'data'=>$data, 'msg'=>'请确认是否预收款冲抵']);

        } else if ($data['syt_yskcd'] == 1) {

            if (empty($data['syt_dcje'])) { // 预收款抵冲金额

                return json(['status'=>201,'data'=>$data, 'msg'=>'预收款抵冲金额有误']);

            }

        }

        // 判断余额加收款金额是否等于应收金额
        if ($data['syt_skje'] !=  round($data['syt_ysje'],0) && $data['syt_skje'] !=  round($data['syt_ysje'],2) && $data['syt_skje'] !=  intval($data['syt_ysje'])) {
            return json(['status'=>201,'data'=>$data, 'msg'=>'收款金额与应收金额不一致']);
        }

        $data['syt_zfsj'] = time();
        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');

        $yssj_ids_arr = explode(',',$data['yssj_ids']);

        $yssj_info = Db::name('yssj')->whereIn('yssj_id',$yssj_ids_arr)->select();

        $fcxx_id_arr = [];
        $yskfy = [];
        foreach ($yssj_info as $yssj_info_key => $yssj_info_item) {

            if (!in_array($yssj_info_item['fcxx_id'],$fcxx_id_arr)) {
                $fcxx_id_arr[] = $yssj_info_item['fcxx_id'];
            }

            if ($yssj_info_item['fylx_id'] == 3) { // 预收费
                $yskfy[$yssj_info_item['fcxx_id']] += $yssj_info_item['yssj_ysje'];
            }
        }

        if (count($fcxx_id_arr) != 1 && !empty($data['syt_dcje'])) {
            return json(['status'=>201,'data'=>$data, 'msg'=>'多个资产一起缴费不可用预收款余额冲抵']);
        }


        if ($fcxx_id_arr[0] != $data['check_fcxx_id']) {

            return json(['status'=>201,'data'=>$data, 'msg'=>'被勾选房间的预收款余额，不可用在其他房间上']);
        }

        $fcxx_id = implode(',',$fcxx_id_arr);

        $syt_data = [
            'fcxx_id'       => $fcxx_id,
            'syt_method'    => $data['skfs_id'], // B Y
            'zkgl_id'       => 0, // 0
            'syt_invoice'   => 0, // 0
            'syt_skje'      => $data['syt_skje'],  // B Y
            'syt_zfsj'      => $data['syt_zfsj'],// B Y
            'syt_zlje'      => 0, // 0
            'syt_bz'        => $data['syt_bz'],  // B Y
            'shop_id'       => $data['shop_id'],
            'xqgl_id'       => $data['xqgl_id'],
            'member_id'     => $data['pay_member_id'],  // B C
            'syt_dcje'      => $data['syt_dcje'],  // B C
            'syt_ysje'      => $data['syt_ysje'],  // B C
        ];

        Db::startTrans();
        try{

            //$syt_id = SytModel::insertGetId($syt_data);
			$res = SytModel::insertGetId($syt_data);
			$syt_id = $res;

            Db::name('yssj')->whereIn('yssj_id',$yssj_ids_arr)->update([
                'syt_id'        => $syt_id,
                'yssj_stuats'   => 1,
                'yssj_fksj'     => $data['syt_zfsj']
            ]);

            if ($data['syt_yskcd'] == 1) {
                $yskcd_insert = [
                    'yssj_fymc'     => '预收费',
                    'yssj_cwyf'     => date('Y-m',$data['syt_zfsj']),
                    'yssj_kstime'   => $data['syt_zfsj'],
                    'yssj_jztime'   => $data['syt_zfsj'],
                    'yssj_fydj'     => floatval('-'.$data['syt_dcje']),
                    'yssj_ysje'     => floatval('-'.$data['syt_dcje']),
                    'fylx_id'       => 3,
                    'fybz_id'       => 0,
                    'yssj_stuats'   => 1,
                    'yssj_fksj'     => $data['syt_zfsj'],
                    'shop_id'       => $data['shop_id'],
                    'xqgl_id'       => $data['xqgl_id'],
                    'member_id'     => $data['pay_member_id'],
                    'syt_id'        => $syt_id,
                    'yssj_cwsh'     => 1
                ];

                Db::name('yssj')->insert($yskcd_insert);
            }

            if (!empty($yskfy)) {
                foreach ($yskfy as $yskfy_key => $yskfy_item) {

                    Db::name('fcxx')
                        ->where('fcxx_id',$yskfy_key)->inc('fcxx_yucun',$yskfy_item)->update();

//                    Db::name('member')
//                        ->where('member_id',$yskfy_key)->inc('member_yucun',$yskfy_item)->update();
                }
            }

            if (!empty($data['syt_dcje'])) {

                Db::name('fcxx')
                    ->whereIn('fcxx_id',$fcxx_id_arr)->dec('fcxx_yucun',$data['syt_dcje'])->update();
//                Db::name('member')->where('member_id',$data['pay_member_id'])
//                    ->dec('member_yucun',$data['syt_dcje'])->update();
            }

            $member = Db::name('member')->where('member_id',$data['pay_member_id'])->find();

            $member_idx_arr = explode(',',$member['member_idx']);

            $member_idx_arr[] = $data['pay_member_id'];

            $member_idx_arr = array_unique($member_idx_arr);

            foreach ($member_idx_arr as $member_idx_arr_item) {

                $member_yssj = Db::name('yssj')->where('member_id',$member_idx_arr_item)
                    ->where('yssj_stuats',0)
                    ->sum('yssj_ysje');
                Db::name('member')->where('member_id',$member_idx_arr_item)->update(['member_yingshou' => $member_yssj]);

            }

            Db::commit();

            $print_ys = Db::name('pjgl')->alias('a')
                ->leftJoin('pjlx b', 'a.pjlx_pid=b.pjlx_id')
                ->where('a.xqgl_id',session('shop.xqgl_id'))
                ->where('a.pjgl_status',1)
                ->where('a.pjlx_id',1)
                ->select()->toArray()[0]['pjlx_wenjian'];

            $data = [
                'syt_id'    => $syt_id,
                'print_ys'  => $print_ys
            ];

            return json(['status'=>200,'data'=>$data, 'msg'=>'收款成功']);

        } catch (\Exception $e) {
            Db::rollback();
            return json(['status'=>200,'data'=>$data, 'msg'=>$e->getMessage()]);
//            throw new ValidateException($e->getMessage());
        }

    }

    public function getMember_id(){
        $limit  = $this->request->post('limit', 20, 'intval');
        $page = $this->request->post('page', 1, 'intval');

        $where = [];
        $skip = ($page-1) * $limit.','.$limit;
        $data = $this->getSelectPageData("select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",$where,$skip);
        return json(['status'=>200,'data'=>$data]);
    }


    /*
     * @Description  获取车位信息
     */
    function getRoleAccessCewei() {
        $where1 = [];
        $where1[] = ['xqgl_id','=',session('shop.xqgl_id')];

        $where = [];
        $where[] = ['a.xqgl_id','=',session('shop.xqgl_id')];

        $tccd = Db::name('tccd')->field('tccd_id,tccd_name')->where($where1)->select(); // 停车场地数组

        $tccd_ids = Db::name('tccd')->field('tccd_id,tccd_name')->where($where1)->column('tccd_id'); // 场地区域数组

        $cwqy = Db::name('cwqy')->field('cwqy_id,cwqy_name')->where($where1)->select(); // 单元信息数组

        $cwqy_ids = Db::name('cwqy')->field('cwqy_id,cwqy_name')->where($where1)->column('cwqy_id'); // 场地区域数组

        $cewei = Db::name('cewei')->alias('a')
            ->field('a.cewei_id,a.cewei_name,a.tccd_id,a.cwqy_id,b.member_name as kh_name,b.member_yingshou,sum(yssj_ysje) as yssj_ysje')
            ->leftJoin('member b','a.member_id = b.member_id')
            ->leftJoin('yssj c','a.cewei_id = c.cewei_id')
            ->where($where)
            ->group('a.cewei_id')->select(); // 车位信息数组 关联 客户


        $nodes_che = [];

        foreach ($tccd as $tccd_item) { //循环停车场地

            $cwqy_children = [];

            foreach ($cwqy as $cwqy_item) { //循环停车区域

                    $cewei_children = [];

                    foreach ($cewei as $cewei_item) {//循环车位

                        if ($cwqy_item['cwqy_id'] == $cewei_item['cwqy_id']
                            && $tccd_item['tccd_id'] == $cewei_item['tccd_id']) { // 停车区域

                            if (!empty($cewei_item['kh_name'] !='')){
                                $kh_name = $cewei_item['kh_name'];
                            }else{
                                $kh_name = "空户";
                            }

                            if($cewei_item['member_yingshou'] > 0 && $cewei_item['yssj_ysje'] != 0){

                                $title = $cewei_item["cewei_name"]."号 ( ".$kh_name." )--欠费";

                            }else{

                                $title = $cewei_item['cewei_name']."号 ( ".$kh_name." )";

                            }

                            $cewei_children[] = [

                                "accessc"    => $cewei_item['cewei_id'],

                                "titlec"     => $title,

                            ];

                        }
                }
                $cwqy_children[] = [

                    'accessc'    => 'QY'.$cwqy_item['cwqy_id'],

                    'titlec'     => $cwqy_item['cwqy_name'],

                    'disabled'  => "true",

                    'childrenc'  => $cewei_children,
                ];
            }

            $nodes_che[] = [

                'accessc'    => 'TC'.$tccd_item['tccd_id'],

                'titlec'     => $tccd_item['tccd_name'],

                'disabled'  => "true",

                'childrenc'  => $cwqy_children

            ];

        }


        return json(['status'=>200,'menusche'=>$nodes_che]);

    }
    /*
     * @Description  获取房产信息
     */
        function getRoleAccessFcxx(){
        $where = [];
        $where[] = ['xqgl_id','=',session('shop.xqgl_id')];
		
        $louyu = Db::name('louyu')->field('louyu_id,louyu_name')->where($where)->whereNull('louyu_pid')->select(); // 楼宇信息数组
		
        $louyu_ids = Db::name('louyu')->where($where)->whereNull('louyu_pid')->column('louyu_id'); // 楼宇 id 数组
		
        $danyuan = Db::name('louyu')->field('louyu_id,louyu_name,louyu_pid')->whereIn('louyu_pid',$louyu_ids)->select(); // 单元信息数组
		
        $danyuan_ids = Db::name('louyu')->whereIn('louyu_pid',$louyu_ids)->column('louyu_id'); // 单元 id 数组
		
        $fcxx = Db::name('fcxx')->alias('a')
			->field('a.fcxx_id,a.fcxx_fjbh,a.louyu_id,b.member_name as kh_name,member_yingshou,sum(yssj_ysje) as yssj_ysje')
			->leftJoin('member b','a.member_id = b.member_id')
            ->leftJoin('yssj c','a.fcxx_id = c.fcxx_id')
			->whereIn('a.louyu_id',$danyuan_ids)
            ->group('a.fcxx_id')
            ->select(); // 房产信息数组

        $cksf = Db::name('fcxx')->alias('a')
			->field('a.fcxx_id,a.fcxx_fjbh,a.louyu_id,b.member_name as kh_name,member_yingshou,sum(yssj_ysje) as yssj_ysje')
			->leftJoin('member b','a.member_id = b.member_id')
            ->leftJoin('yssj c','a.fcxx_id = c.fcxx_id')
			->whereIn('a.louyu_id',$louyu_ids)
            ->group('a.fcxx_id')->select(); // 车库商服信息数组

        $nodes = [];

        foreach ($louyu as $louyu_item) { //循环楼宇

            $danyuan_children = [];

            foreach ($danyuan as $danyuan_item) { //循环单元

                if ($louyu_item['louyu_id'] == $danyuan_item['louyu_pid']) {

                    $fcxx_children = [];

                    foreach ($fcxx as $fcxx_item) {//循环房间

                        if ($danyuan_item['louyu_id'] == $fcxx_item['louyu_id']) {
							
							if (!empty($fcxx_item['kh_name'] !='')){
								$kh_name = $fcxx_item['kh_name'];
							}else{
								$kh_name = "空户";
							}

							if($fcxx_item['member_yingshou'] > 0 && $fcxx_item['yssj_ysje'] != 0){

							    $title = $fcxx_item["fcxx_fjbh"]."( ".$kh_name." )--欠费";
								
							}else{
									
							    $title = $fcxx_item['fcxx_fjbh']."( ".$kh_name." )";
								
							}

                            $fcxx_children[] = [

                                "access"    => $fcxx_item['fcxx_id'],
								
								"title"     => $title,

                            ];

                        }

                    }

                    $danyuan_children[] = [

                        'access'    => 'D'.$danyuan_item['louyu_id'],

                        'title'     => $danyuan_item['louyu_name'],
						
						'disabled'  => "true",

                        'children'  => $fcxx_children,

                    ];

                }

            }

            $cksf_children = [];

            foreach ($cksf as $cksf_item) {

                if ($louyu_item['louyu_id'] == $cksf_item['louyu_id']) {
					
					if (!empty($cksf_item['kh_name'] !='')){
								$kh_name = $cksf_item['kh_name'];
							}else{
								$kh_name = "空户";
					}
						
						if($cksf_item['member_yingshou'] > 0 && $cksf_item['yssj_ysje'] != 0){

						    $title = $cksf_item['fcxx_fjbh']."( ".$kh_name." )--欠费";
								
						}else{
									
						    $title = $cksf_item['fcxx_fjbh']."( ".$kh_name." )";
								
						}

                    $cksf_children[] = [

                        "access"    => $cksf_item['fcxx_id'],

                        "title"     => $title,

                    ];

                }

            }

            $danyuan_children[] = [

                'access'    => 'C'.$louyu_item['louyu_id'],

                'title'     => '车库商服',
				
				'disabled'  => "true",
				
                'children'  => $cksf_children

            ];

            $nodes[] = [

                'access'    => 'L'.$louyu_item['louyu_id'],

                'title'     => $louyu_item['louyu_name'],
				
				'disabled'  => "true",

                'children'  => $danyuan_children

            ];

        }

        return json(['status'=>200,'menus'=>$nodes]);

    }

    function getFybz_id() {
        $fydy_id =  $this->request->post('fydy_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select fybz_id,fybz_name from cd_fybz where xqgl_id='.session('shop.xqgl_id').' and fydy_id ='.$fydy_id,'mysql');
        return json($data);
    }

    function submitCwfy() {

        $postField = 'cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $fybz = Db::name('fybz')->where('fybz_id',$data['fybz_id'])->find();

        $cwfy = [];
        $member_id_column = [];
        if ($data['cwfy_scfs'] == 1) { // 生成方式 按月生成

            $cwfy_ksmonth = $data['cwfy_ksmonth'].'-01';
            $cwfy_zzmonth = $data['cwfy_zzmonth'].'-01';

            $data['cwfy_ksmonth'] = !empty($data['cwfy_ksmonth']) ? strtotime($cwfy_ksmonth) : ''; // 开始月份
            $data['cwfy_zzmonth'] = !empty($data['cwfy_zzmonth']) ? strtotime("$cwfy_zzmonth +1 month -1 day") : ''; // 终止月份

            if ($data['cwfy_ksmonth'] > $data['cwfy_zzmonth']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $cewei_yssj = Db::name('yssj')
                ->where('cewei_id',$data['cewei_id'])
                ->order('yssj_id','desc')->find();

            if ($data['cwfy_ksmonth'] <= $cewei_yssj['yssj_jztime']) {
                return json(['status'=>201,'msg'=>'生成时间应大于'.date('Y-m-d',$cewei_yssj['yssj_jztime'])]);
            }

            $calculate_month = $this->calculateMonth($data);

            foreach ($calculate_month as $calculate_month_item) {

                $cwfy[] = [
                    'yssj_fymc'     => $fybz['fybz_name'],
                    'fydy_id'       => $data['fydy_id'],
                    'yssj_cwyf'     => $calculate_month_item['cwyf'],
                    'yssj_kstime'   => $calculate_month_item['kstime'],
                    'yssj_jztime'   => $calculate_month_item['jztime'],
                    'yssj_fydj'     => $fybz['fybz_bzdj'],
                    'yssj_ysje'     => $fybz['fybz_bzdj'],
                    'fylx_id'       => 1,
                    'fybz_id'       => $data['fybz_id'],
                    'yssj_stuats'   => 0,
                    'yssj_fksj'     => '',
                    'shop_id'       => session('shop.shop_id'),
                    'xqgl_id'       => session('shop.xqgl_id'),
                    'member_id'     => $data['member_id'],
                    'syt_id'        => null,
                    'cewei_id'      => $data['cewei_id'],
                    'sjlx_id'       => 2
                ];

                $member_id_column[$data['member_id']] = $data['member_id'];
            }
        }

        if ($data['cwfy_scfs'] == 2) { // 生成方式 按日生成

            $data['cwfy_kstime'] = !empty($data['cwfy_kstime']) ? strtotime($data['cwfy_kstime']) : ''; // 开始时间
            $data['cwfy_zztime'] = !empty($data['cwfy_zztime']) ? strtotime($data['cwfy_zztime']) : ''; // 终止时间

            if ($data['cwfy_kstime'] > $data['cwfy_zztime']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            $cewei_yssj = Db::name('yssj')->where('cewei_id',$data['cewei_id'])
                ->order('yssj_id','desc')->find();

            if ($data['cwfy_kstime'] <= $cewei_yssj['yssj_jztime']) {
                return json(['status'=>201,'msg'=>'生成时间应大于'.date('Y-m-d',$cewei_yssj['yssj_jztime'])]);
            }

            $calculate_days = $this->calculateDays($data,$fybz);

            foreach ($calculate_days as $calculate_days_item) {

                $cwfy[] = [
                    'yssj_fymc'     => $fybz['fybz_name'],
                    'fydy_id'       => $data['fydy_id'],
                    'yssj_cwyf'     => $calculate_days_item['cwyf'],
                    'yssj_kstime'   => $calculate_days_item['kstime'],
                    'yssj_jztime'   => $calculate_days_item['jztime'],
                    'yssj_fydj'     => $fybz['fybz_bzdj'],
                    'yssj_ysje'     => $calculate_days_item['ysje'],
                    'fylx_id'       => 1,
                    'fybz_id'       => $data['fybz_id'],
                    'yssj_stuats'   => 0,
                    'yssj_fksj'     => '',
                    'shop_id'       => session('shop.shop_id'),
                    'xqgl_id'       => session('shop.xqgl_id'),
                    'member_id'     => $data['member_id'],
                    'syt_id'        => null,
                    'cewei_id'      => $data['cewei_id'],
                    'sjlx_id'       => 2
                ];

                $member_id_column[$data['member_id']] = $data['member_id'];
            }
        }

        $yssj = new YssjModel();
        $yssj->saveAll($cwfy);

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

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

    function calculateDays($data,$fybz) {

        $sTime = strtotime(date('Y-m-01', $data['cwfy_kstime']));
        $eTime = strtotime(date('Y-m-01', $data['cwfy_zztime']));

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

        $month[0]['kstime'] = strtotime(date('Y-m-d', $data['cwfy_kstime']));
        $month[count($month)-1]['jztime'] =  $data['cwfy_zztime'];

        $months = $month;
        foreach ($month as $month_key => $month_item) {
            if ($month_key == 0) {
                // 计算残月 金额
                $months[$month_key]['ysje'] = $this->calculateM($data,$fybz,$month_item);
            } elseif ($month_key == count($month)-1) {
                $months[$month_key]['ysje'] = $this->calculateM($data,$fybz,$month_item);
            } else {
                $months[$month_key]['ysje'] = $fybz['fybz_bzdj'];
            }

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

    function calculateM($data,$fybz,$month_item) {
        $ysje = 0;
        $day = (($month_item['jztime'] - $month_item['kstime'])/86400)+1;

        if ($data['cwfy_sclx'] == 1) { // 30天
            $ysje = ($fybz['fybz_bzdj'] / 30) * $day;
        }

        if ($data['cwfy_sclx'] == 2) { // 实际天数
            $cwyf = $month_item['cwyf'].'-01';
            $cwyf_s = strtotime($cwyf);
            $cwyf_e = strtotime("$cwyf +1 month -1 day");
            $d = (($cwyf_e - $cwyf_s)/86400)+1;
            $ysje = round (($fybz['fybz_bzdj'] / $d) * $day,2);
        }
        return $ysje;
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

    function calculateMonthNum($strtotime1,$strtotime2) {

        $y = date('Y',$strtotime1);

        $ys = date('Y',$strtotime2);

        $m = (int)date('m',$strtotime1);

        $ms = (int)date('m',$strtotime2);

        $chaY = $ys - $y;

        //月份相差多少
        $chaM = 12 - $m + $ms;

        //相差一年就加12
        return $chaM + (($chaY-1) *12)+1;
    }

    function calculateMonth($data) {

        $sDate = $data['cwfy_ksmonth'];
        $eDate = $data['cwfy_zzmonth'];

        if ((isset($data['scys_ksyf']) && !empty($data['scys_ksyf']))
            || (isset($data['scys_jsyf']) && !empty($data['scys_jsyf']))) {

            $sDate = $data['scys_ksyf'];
            $eDate = $data['scys_jsyf'];

        }

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

    function printIndexLists() {
        if (!$this->request->isPost()){
//dump(session('shop'));exit;
            $print_ys = $this->request->get('print_ys', '', 'serach_in');

            return view($print_ys);
        }else{
            $syt_id = $this->request->post('syt_id', '', 'serach_in');
            $pjlx_id = $this->request->post('pjlx_id', '', 'serach_in');

            $where = [];
            $where[] = ['a.syt_id', '=', $syt_id];

            $field = 'a.syt_id,a.syt_skje,a.syt_zfsj,a.syt_method,a.shop_id,
            b.fcxx_id,b.member_id,b.yssj_fymc,
            sum(b.yssj_ysje) as yssj_ysje,
            min(b.yssj_kstime) as yssj_kstime,
            max(b.yssj_jztime) as yssj_jztime,
            b.yssj_fydj,b.cbgl_id,b.cewei_id,
            c.member_name,
            d.fcxx_fjbh,d.louyu_id';

            $syt = Db::name('syt')->alias('a')
                ->field($field)
                ->leftJoin('yssj b','a.syt_id=b.syt_id')
                ->leftJoin('member c','a.member_id=c.member_id')
                ->leftJoin('fcxx d','b.fcxx_id=d.fcxx_id')
                ->where($where)
                ->group('b.yssj_fymc,b.cewei_id,b.fcxx_id')
                ->select()->toArray();

            $syt_count = Db::name('syt')->alias('a')
                ->field($field)
                ->leftJoin('yssj b','a.syt_id=b.syt_id')
                ->leftJoin('member c','b.member_id=c.member_id')
                ->leftJoin('fcxx d','b.fcxx_id=d.fcxx_id')
                ->where($where)
                ->group('b.yssj_fymc,b.cewei_id,b.fcxx_id')
                ->count();


            $res_data = $syt;

            foreach ($syt as $syt_key => $syt_item) {

                if (!empty($syt_item['louyu_id']) || !empty($syt_item['fcxx_id'])) {

                    $danyuan = Db::name('louyu')->field('louyu_pid,louyu_name')
                        ->where('louyu_id',$syt_item['louyu_id'])->find();

                    $louyu = Db::name('louyu')->where('louyu_id',$danyuan['louyu_pid'])->value('louyu_name');

                    $res_data[$syt_key]['fcxx_fjbh'] = $louyu.'_'.$danyuan['louyu_name'].'_'.$syt_item['fcxx_fjbh'];

                }
                if (!empty($syt_item['cewei_id'])) {

                    $cewei = Db::name('cewei')->alias('a')
                        ->field('a.cewei_name,b.cwqy_name,c.tccd_name')
                        ->leftJoin('cwqy b','a.cwqy_id=b.cwqy_id')
                        ->leftJoin('tccd c','a.tccd_id=c.tccd_id')
                        ->where('a.cewei_id',$syt_item['cewei_id'])->select();

                    $res_data[$syt_key]['fcxx_fjbh'] = $cewei[0]['tccd_name'].'_'.$cewei[0]['cwqy_name'].'_'.$cewei[0]['cewei_name'];
                }

                if (empty($syt_item['cbgl_id'])) {
                    continue;
                }


                //重新整理水费
//                $cbgl = Db::name('cbgl')->field('cbgl_sqsl,cbgl_bqsl,cbgl_cbyl,cbgl_ybbl')
//                    ->where('cbgl_id',$syt_item['cbgl_id'])->find();

                $cbgl = Db::name('syt')->alias('a')
                    ->field('min(c.cbgl_sqsl) as cbgl_sqsl,max(c.cbgl_bqsl) as cbgl_bqsl, sum(c.cbgl_cbyl) as cbgl_cbyl,c.cbgl_ybbl')
                    ->leftJoin('yssj b','a.syt_id=b.syt_id')
                    ->leftJoin('cbgl c','c.cbgl_id=b.cbgl_id')
                    ->where($where)
                    ->whereNotNull('b.cbgl_id')
                    ->select()->toArray();

                $res_data[$syt_key]['cbgl_sqsl'] = $cbgl[0]['cbgl_sqsl'];
                $res_data[$syt_key]['cbgl_bqsl'] = $cbgl[0]['cbgl_bqsl'];
                $res_data[$syt_key]['cbgl_cbyl'] = $cbgl[0]['cbgl_cbyl'];
                $res_data[$syt_key]['cbgl_ybbl'] = $cbgl[0]['cbgl_ybbl'];

            }

            $res = [
                'total'         => $syt_count,
                'per_page'      => 10000,
                'current_page'  => 1,
                'last_page'     => 1,
                'data'          => $res_data
            ];


            $member_name = Db::name('member')->where('member_id',$syt[0]['member_id'])->value('member_name');
            $shop_skdw = Db::name('shop')->where('shop_id',$syt[0]['shop_id'])->value('shop_skdw');

            $syt_method = '微信';

            switch ($syt[0]['syt_method']) {
                case 1:
                    break;
                case 2:
                    $syt_method = '支付宝';
                    break;
                case 3:
                    $syt_method = '现金';
                    break;
            }

            $data['title_name'] = Db::name('pjgl')
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('pjgl_status',1)
                ->where('pjlx_id',$pjlx_id)
                ->value('pjgl_name');
            
            $data['bill_sn'] = date('Ymd',time()).'-'.$syt_id;
            $data['pay_sn'] = 'BH'.str_pad($syt_id,11,0,STR_PAD_LEFT);
            $data['pay_member_name'] = $member_name;
            $data['total_money'] = $syt[0]['syt_skje'];
            $data['yssj_fksj'] = date('Y-m-d',$syt[0]['syt_zfsj']);
            $data['syt_method'] = $syt_method;
            $data['shop_skdw'] = $shop_skdw;

            $data['status'] = 200;
            $data['data'] = $res;

            return json($data);
        }
    }

    function getPjys() {

        $pjlx_id = $this->request->get('pjlx_id', '', 'serach_in');

        $print_ys = Db::name('pjgl')->alias('a')
            ->leftJoin('pjlx b', 'a.pjlx_pid=b.pjlx_id')
            ->where('a.xqgl_id',session('shop.xqgl_id'))
            ->where('a.pjgl_status',1)
            ->where('a.pjlx_id',$pjlx_id)
            ->select()->toArray()[0]['pjlx_wenjian'];

        $data = [
            'print_ys'  => $print_ys
        ];

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

    function printIndexTzd() {

        if (!$this->request->isPost()){

            $print_ys = $this->request->get('print_ys', '', 'serach_in');

            return view($print_ys);
        }else{
            $member_id = $this->request->post('member_id', '', 'serach_in');
            $ids = $this->request->post('ids', '', 'serach_in');
            $pjlx_id = $this->request->post('pjlx_id', '', 'serach_in');

            $yssj_id = explode(',',$ids);

            $where = [];
            $where[] = ['a.member_id', '=', $member_id];
            $where[] = ['a.yssj_id', 'in', $yssj_id];

            $field = 'a.fcxx_id,a.member_id,a.yssj_fymc,
            sum(a.yssj_ysje) as yssj_ysje,
            min(a.yssj_kstime) as yssj_kstime,
            max(a.yssj_jztime) as yssj_jztime,
            a.yssj_fydj,a.cbgl_id,a.cewei_id,
            b.member_name,
            c.fcxx_fjbh,c.louyu_id';

            $yssj = Db::name('yssj')->alias('a')
                ->field($field)
                ->leftJoin('member b','a.member_id=b.member_id')
                ->leftJoin('fcxx c','a.fcxx_id=c.fcxx_id')
                ->where($where)
                ->group('a.yssj_fymc,a.cewei_id,a.fcxx_id')
                ->select()->toArray();

            $yssj_count = Db::name('yssj')->alias('a')
                ->leftJoin('member b','a.member_id=b.member_id')
                ->leftJoin('fcxx c','a.fcxx_id=c.fcxx_id')
                ->where($where)
                ->group('a.yssj_fymc,a.cewei_id,a.fcxx_id')
                ->count();

            $res_data = $yssj;

            $total_money = 0;
            foreach ($yssj as $yssj_key => $yssj_item) {

                if (!empty($yssj_item['louyu_id']) || !empty($yssj_item['fcxx_id'])) {

                    $danyuan = Db::name('louyu')->field('louyu_pid,louyu_name')
                        ->where('louyu_id',$yssj_item['louyu_id'])->find();

                    $louyu = Db::name('louyu')->where('louyu_id',$danyuan['louyu_pid'])->value('louyu_name');

                    $res_data[$yssj_key]['fcxx_fjbh'] = $louyu.'_'.$danyuan['louyu_name'].'_'.$yssj_item['fcxx_fjbh'];

                }

                if (!empty($yssj_item['cewei_id'])) {

                    $cewei = Db::name('cewei')->alias('a')
                        ->field('a.cewei_name,b.cwqy_name,c.tccd_name')
                        ->leftJoin('cwqy b','a.cwqy_id=b.cwqy_id')
                        ->leftJoin('tccd c','a.tccd_id=c.tccd_id')
                        ->where('a.cewei_id',$yssj_item['cewei_id'])->select();

                    $res_data[$yssj_key]['fcxx_fjbh'] = $cewei[0]['tccd_name'].'_'.$cewei[0]['cwqy_name'].'_'.$cewei[0]['cewei_name'];
                }

                $total_money += $yssj_item['yssj_ysje'];

                if (empty($yssj_item['cbgl_id'])) {
                    continue;
                }

                $cbgl = Db::name('cbgl')->field('cbgl_sqsl,cbgl_bqsl,cbgl_cbyl,cbgl_ybbl')
                    ->where('cbgl_id',$yssj_item['cbgl_id'])->find();

                $res_data[$yssj_key]['cbgl_sqsl'] = $cbgl['cbgl_sqsl'];
                $res_data[$yssj_key]['cbgl_bqsl'] = $cbgl['cbgl_bqsl'];
                $res_data[$yssj_key]['cbgl_cbyl'] = $cbgl['cbgl_cbyl'];
                $res_data[$yssj_key]['cbgl_ybbl'] = $cbgl['cbgl_ybbl'];

            }

            $res = [
                'total'         => $yssj_count,
                'per_page'      => 10000,
                'current_page'  => 1,
                'last_page'     => 1,
                'data'          => $res_data
            ];

            $member_name = Db::name('member')->where('member_id',$member_id)->value('member_name');

            $data['title_name'] = Db::name('pjgl')
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('pjgl_status',1)
                ->where('pjlx_id',$pjlx_id)
                ->value('pjgl_name');

            $data['tzsj_time'] = date('Y-m-d',time());
            $data['pay_member_name'] = $member_name;
            $data['total_money'] = $total_money;

            $data['status'] = 200;
            $data['data'] = $res;

            return json($data);
        }
    }

    function getDanyuan_id() {
        $louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select louyu_id,louyu_name from cd_louyu where xqgl_id='.session('shop.xqgl_id').' and '.'(louyu_pid ='.$louyu_id.' or louyu_id ='.$louyu_id.')','mysql');
        return json($data);
    }

    function getFcxx_id() {
        $danyuan_id =  $this->request->post('danyuan_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where xqgl_id='.session('shop.xqgl_id').' and louyu_id ='.$danyuan_id,'mysql');
        return json($data);
    }

    public function submitScys() {

        $postField = 'shop_id,xqgl_id,scys_ksyf,scys_jsyf,scys_kstime,scys_zztime,jflx_id,louyu_id,fydy_id,danyuan_id,fcxx_id,scys_scfs,scys_sclx';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');

        // 获取费用标准
        $fybz = Db::name('fybz')
            ->field('fylx_id,fybz_id,fydy_id,fybz_name,fybz_bzdj')
            ->whereIn('fydy_id',$data['fydy_id'])
            ->select();

        $where_fyfp_fcxx = [];
        $where_fyfp_fcxx[] = ['b.fcxx_id','=',$data['fcxx_id']];

        $scys = [];
        $member_id_arr = [];

        $scys_ksyf = '';
        $scys_jsyf = '';
        if ($data['scys_scfs'] == 1) {

            $scys_ksyf_s = $data['scys_ksyf'].'-01';
            $scys_jsyf_s = $data['scys_jsyf'].'-01';

            $scys_ksyf = $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($scys_ksyf_s) : ''; // 开始月份
            $scys_jsyf = $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime("$scys_jsyf_s +1 month -1 day") : ''; // 终止月份

            if ($data['scys_ksyf'] > $data['scys_jsyf']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            foreach ($data['fydy_id'] as $fydy_id ) {

                $fcxx_yssj = Db::name('yssj')
                    ->where('fydy_id',$fydy_id)
                    ->where('fcxx_id',$data['fcxx_id'])
                    ->where('shop_id',$data['shop_id'])
                    ->whereNotNull('scys_id')
                    ->where('scys_id','>',0)
                    ->order('yssj_id','desc')->find();

                if (!empty($fcxx_yssj)) {
                    if ($data['scys_ksyf'] != strtotime('+1 day', $fcxx_yssj['yssj_jztime'])) {
                        return json([
                            'status'=>201,
                            'msg'=>"生成日期应从 ".date('Y-m-d',strtotime('+1 day', $fcxx_yssj['yssj_jztime']))."开始"
                        ]);
                    }
                }
            }
        }

        if ($data['scys_scfs'] == 2) {

            $scys_ksyf = $data['scys_kstime'] = !empty($data['scys_kstime']) ? strtotime($data['scys_kstime']) : ''; // 开始时间
            $scys_jsyf = $data['scys_zztime'] = !empty($data['scys_zztime']) ? strtotime($data['scys_zztime']) : ''; // 终止时间

            if ($data['scys_kstime'] > $data['scys_zztime']) {
                return json(['status'=>201,'msg'=>'开始时间不应大于结束时间']);
            }

            foreach ($data['fydy_id'] as $fydy_id ) {

                $fcxx_yssj = Db::name('yssj')
                    ->where('fydy_id',$fydy_id)
                    ->where('fcxx_id',$data['fcxx_id'])
                    ->where('shop_id',$data['shop_id'])
                    ->whereNotNull('scys_id')
                    ->where('scys_id','>',0)
                    ->order('yssj_id','desc')->find();

                if (!empty($fcxx_yssj)) {
                    if ($data['scys_kstime'] != strtotime('+1 day', $fcxx_yssj['yssj_jztime'])) {
                        return json([
                            'status'=>201,
                            'msg'=>"生成日期应从 ".date('Y-m-d',strtotime('+1 day', $fcxx_yssj['yssj_jztime']))."开始"
                        ]);
                    }
                }
            }

        }

        $data_scys = [
            'scys_ksyf' => $scys_ksyf,
            'scys_jsyf' => $scys_jsyf,
            'jflx_id'   => $data['jflx_id'],
            'louyu_id'  => $data['louyu_id'],
            'fydy_id'   => implode(',',$data['fydy_id']),
            'shop_id'   => $data['shop_id'],
            'xqgl_id'   => $data['xqgl_id'],
            'scys_scfs' => $data['scys_scfs'],
            'scys_sclx' => $data['scys_sclx'],
            'scys_scgc' => 2,
        ];

        $scys_id = Db::name('scys')->insertGetId($data_scys);

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

                            $scys[] = [
                                'scys_id'       => $scys_id,
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
                                'fcxx_id'       => $data['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                    if ($data['scys_scfs'] == 2) { // 生成方式 按日生成

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

                            $scys[] = [
                                'scys_id'       => $scys_id,
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
                                'fcxx_id'       => $data['fcxx_id'],
                                'sjlx_id'       => 1
                            ];

                        }

                    }

                }

            }

        }

        if (!empty($scys)) {
            $yssjM = new YssjModel();
            $yssjM->saveAll($scys);
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

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);

    }
	/*end*/
}

