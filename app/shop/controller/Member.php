<?php 
/*
 module:		客户信息控制器
 create_time:	2023-01-13 08:04:18
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Member as MemberModel;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Member extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail','glfangchan','getGlfangchanInfo','glchewei','getGlcheweiInfo','glcar','getGlcarInfo'])){
			$idx = $this->request->post('member_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = MemberModel::find($v);
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
		if (!$this->request->isPost()){
			return view('index');
		}else{
			$limit  = $this->request->post('limit', 20, 'intval');
			$page = $this->request->post('page', 1, 'intval');

			$where = [];
			$where['member_id'] = $this->request->post('member_id', '', 'serach_in');

			$where['member.shop_id'] = session('shop.shop_id');

			$where['member.xqgl_id'] = session('shop.xqgl_id');
			$where['member.member_name'] = ['like',$this->request->post('member_name', '', 'serach_in')];
			$where['member.member_tel'] = ['like',$this->request->post('member_tel', '', 'serach_in')];
			$where['member.member_sex'] = ['like',$this->request->post('member_sex', '', 'serach_in')];
			$where['member.zjlx_id'] = ['like',$this->request->post('zjlx_id', '', 'serach_in')];
			$where['member.member_zjhm'] = ['like',$this->request->post('member_zjhm', '', 'serach_in')];
			$where['member.khlb_id'] = $this->request->post('khlb_id', '', 'serach_in');
			$where['member.fcxx_idx'] = ['like',$this->request->post('fcxx_idx', '', 'serach_in')];
			$where['member.cewei_id'] = ['find in set',$this->request->post('cewei_id', '', 'serach_in')];

			$field = 'member_id,member_name,member_tel,member_sex,member_zjhm,khlb_id,khlx_id,member_yucun,member_yingshou';

			$withJoin = [
				'zjlx'=>explode(',','zjlx_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'member_id desc';

			$query = MemberModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['zjlx_id']){
					$res['data'][$k]['zjlx_id'] = Db::query("select zjlx_name from  cd_zjlx where zjlx_id=".$v['zjlx_id']."")[0]['zjlx_name'];
				}
				if($v['khlb_id']){
					$res['data'][$k]['khlb_id'] = Db::query("select khlb_name from  cd_khlb where khlb_id=".$v['khlb_id']."")[0]['khlb_name'];
				}
				if($v['khlx_id']){
					$res['data'][$k]['khlx_id'] = Db::query("select khlx_name from  cd_khlx where khlx_id=".$v['khlx_id']."")[0]['khlx_name'];
				}
				if($v['member_idx']){
					$res['data'][$k]['member_idx'] = Db::query("select member_name from  cd_member where shop_id = ".session("shop.shop_id")." and xqgl_id = ".session("shop.xqgl_id")." and member_id=".$v['member_idx']."")[0]['member_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$data['sum_member_yucun'] = $query->where(formatWhere($where))->sum('member_yucun');
			$data['sum_member_yingshou'] = $query->where(formatWhere($where))->sum('member_yingshou');
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('zjlx_id,khlb_id,khlx_id,member_idx,cewei_id,car_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'member_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['member_id']) throw new ValidateException ('参数错误');
		MemberModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,member_name,member_tel,member_birthday,member_sex,zjlx_id,member_zjhm,khlb_id,khlx_id,member_idx,member_hksl,member_khzy,member_hkdz,member_gzdw,member_gsjj,member_remark,member_fyqrrq,member_rhtzrq,member_yzzp,member_zjzp';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Member::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['member_birthday'] = !empty($data['member_birthday']) ? strtotime($data['member_birthday']) : '';
		$data['member_idx'] = implode(',',$data['member_idx']);
		$data['member_fyqrrq'] = !empty($data['member_fyqrrq']) ? strtotime($data['member_fyqrrq']) : '';
		$data['member_rhtzrq'] = !empty($data['member_rhtzrq']) ? strtotime($data['member_rhtzrq']) : '';

		try{
			$res = MemberModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'member_id,shop_id,xqgl_id,member_name,member_tel,member_birthday,member_sex,zjlx_id,member_zjhm,khlb_id,khlx_id,member_idx,member_hksl,member_khzy,member_hkdz,member_gzdw,member_gsjj,member_remark,member_fyqrrq,member_rhtzrq,member_yzzp,member_zjzp';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Member::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['member_birthday'] = !empty($data['member_birthday']) ? strtotime($data['member_birthday']) : '';
		$data['member_idx'] = implode(',',$data['member_idx']);
		$data['member_fyqrrq'] = !empty($data['member_fyqrrq']) ? strtotime($data['member_fyqrrq']) : '';
		$data['member_rhtzrq'] = !empty($data['member_rhtzrq']) ? strtotime($data['member_rhtzrq']) : '';

		try{
			MemberModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,shop_id,xqgl_id,member_name,member_tel,member_birthday,member_sex,zjlx_id,member_zjhm,khlb_id,khlx_id,member_idx,member_hksl,member_khzy,member_hkdz,member_gzdw,member_gsjj,member_remark,member_fyqrrq,member_rhtzrq,member_yzzp,member_zjzp';
		$res = MemberModel::field($field)->find($id);
		$res['member_idx'] = explode(',',$res['member_idx']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  权限节点
 	*/
	function getRoleAccess(){
		$nodes = (new \utils\AuthAccess())->getNodeMenus(298,0);

		if($baseNode = hook('hook/Base@getShopNode',$nodes)){
			$nodes = array_merge($baseNode,$nodes);
		}

		array_multisort(array_column($nodes, 'sortid'),SORT_ASC,$nodes );
		return json(['status'=>200,'menus'=>$nodes]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('member_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Member@beforShopDelete',$idx)){
            return $ret;
        }

        MemberModel::destroy(['member_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  导入
 	*/
	public function importData(){
		$data = $this->request->post();
		$list = [];
		foreach($data as $key=>$val){
			$list[$key]['shop_id'] = session('shop.shop_id');
			$list[$key]['xqgl_id'] = session('shop.xqgl_id');
			$list[$key]['member_name'] = $val['客户名称'];
			$list[$key]['member_tel'] = $val['客户手机'];
			$list[$key]['member_birthday'] = strtotime($val['客户生日']);
			$list[$key]['member_sex'] = getValByKey($val['客户性别'],'[{"key":"男","val":"1","label_color":"primary"},{"key":"女","val":"2","label_color":"warning"}]');
			$list[$key]['zjlx_id'] = $val['证件类型'];
			$list[$key]['member_zjhm'] = $val['证件号码'];
			$list[$key]['khlb_id'] = $val['客户类别'];
			$list[$key]['khlx_id'] = $val['客户类型'];
			$list[$key]['member_idx'] = $val['家庭成员'];
			$list[$key]['fcxx_idx'] = $val['房产信息'];
			$list[$key]['cewei_id'] = $val['车位信息'];
			$list[$key]['car_id'] = $val['车辆信息'];
			$list[$key]['member_hksl'] = $val['住卡数量'];
			$list[$key]['member_khzy'] = $val['客户职业'];
			$list[$key]['member_hkdz'] = $val['户口地址'];
			$list[$key]['member_gzdw'] = $val['工作单位'];
			$list[$key]['member_gsjj'] = $val['公司简介'];
			$list[$key]['member_remark'] = $val['备注信息'];
			$list[$key]['member_fyqrrq'] = strtotime($val['房源确认']);
			$list[$key]['member_rhtzrq'] = strtotime($val['入户通知']);
			$list[$key]['member_yzzp'] = $val['业主照片'];
			$list[$key]['member_zjzp'] = $val['证件照片'];
		}
		(new MemberModel)->insertAll($list);
		return json(['status'=>200]);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['member_id'] = ['in',$this->request->param('member_id', '', 'serach_in')];

		$where['member.shop_id'] = session('shop.shop_id');

		$where['member.xqgl_id'] = session('shop.xqgl_id');
		$where['member.member_name'] = ['like',$this->request->param('member_name', '', 'serach_in')];
		$where['member.member_tel'] = ['like',$this->request->param('member_tel', '', 'serach_in')];
		$where['member.member_sex'] = ['like',$this->request->param('member_sex', '', 'serach_in')];
		$where['member.zjlx_id'] = ['like',$this->request->param('zjlx_id', '', 'serach_in')];
		$where['member.member_zjhm'] = ['like',$this->request->param('member_zjhm', '', 'serach_in')];
		$where['member.khlb_id'] = $this->request->param('khlb_id', '', 'serach_in');
		$where['member.fcxx_idx'] = ['like',$this->request->param('fcxx_idx', '', 'serach_in')];
		$where['member.cewei_id'] = ['find in set',$this->request->param('cewei_id', '', 'serach_in')];

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'member_id desc';

		$field = 'member_name,member_tel,member_birthday,member_sex,member_remark,member_fyqrrq,member_hksl,zjlx_id,member_zjhm,member_khzy,member_rhtzrq,member_hkdz,khlb_id,khlx_id,member_gzdw,member_gsjj,member_idx';

		$withJoin = [
			'shop'=>explode(',','shop_name'),
			'xqgl'=>explode(',','xqgl_name'),
		];

		$res = MemberModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$cache_key = 'Member';
		if($page == 1){
			cache($cache_key,null);
			cache($cache_key,[]);
		}
		if($res['data']){
			cache($cache_key,array_merge(cache($cache_key),$res['data']));
			$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
			$data['state'] =  'ok';
			return json($data);
		}
		if($state == 'ok'){
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1','客户名称');
			$sheet->setCellValue('B1','客户手机');
			$sheet->setCellValue('C1','客户生日');
			$sheet->setCellValue('D1','客户性别');
			$sheet->setCellValue('E1','证件类型');
			$sheet->setCellValue('F1','证件号码');
			$sheet->setCellValue('G1','客户类别');
			$sheet->setCellValue('H1','客户类型');
			$sheet->setCellValue('I1','家庭成员');
			$sheet->setCellValue('J1','住卡数量');
			$sheet->setCellValue('K1','客户职业');
			$sheet->setCellValue('L1','户口地址');
			$sheet->setCellValue('M1','工作单位');
			$sheet->setCellValue('N1','公司简介');
			$sheet->setCellValue('O1','备注信息');
			$sheet->setCellValue('P1','房源确认');
			$sheet->setCellValue('Q1','入户通知');
			$sheet->setCellValue('R1','公司名称');
			$sheet->setCellValue('S1','项目名称');

			foreach(cache($cache_key) as $k=>$v){
				$sheet->setCellValue('A'.($k+2),$v['member_name']);
				$sheet->setCellValue('B'.($k+2),$v['member_tel']);
				$sheet->setCellValue('C'.($k+2),!empty($v['member_birthday']) ? date('Y-m-d',$v['member_birthday']) : '');
				$sheet->setCellValue('D'.($k+2),getItemVal($v['member_sex'],'[{"key":"男","val":"1","label_color":"primary"},{"key":"女","val":"2","label_color":"warning"}]'));
				$sheet->setCellValue('E'.($k+2),$v['zjlx_id']);
				$sheet->setCellValue('F'.($k+2),$v['member_zjhm']);
				$sheet->setCellValue('G'.($k+2),$v['khlb_id']);
				$sheet->setCellValue('H'.($k+2),$v['khlx_id']);
				$sheet->setCellValue('I'.($k+2),$v['member_idx']);
				$sheet->setCellValue('J'.($k+2),$v['member_hksl']);
				$sheet->setCellValue('K'.($k+2),$v['member_khzy']);
				$sheet->setCellValue('L'.($k+2),$v['member_hkdz']);
				$sheet->setCellValue('M'.($k+2),$v['member_gzdw']);
				$sheet->setCellValue('N'.($k+2),$v['member_gsjj']);
				$sheet->setCellValue('O'.($k+2),$v['member_remark']);
				$sheet->setCellValue('P'.($k+2),!empty($v['member_fyqrrq']) ? date('Y-m-d',$v['member_fyqrrq']) : '');
				$sheet->setCellValue('Q'.($k+2),!empty($v['member_rhtzrq']) ? date('Y-m-d',$v['member_rhtzrq']) : '');
				$sheet->setCellValue('R'.($k+2),$v['shop']['shop_name']);
				$sheet->setCellValue('S'.($k+2),$v['xqgl']['xqgl_name']);
			}

			$filename = '客户信息.'.config('my.dump_extension');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename);
 			header('Cache-Control: max-age=0');
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');exit;
		}
	}


	/*start*/
	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,member_name,member_tel,member_birthday,member_sex,cewei_id,car_id,member_hksl,member_khzy,member_hkdz,member_gzdw,member_gsjj,member_remark,member_fyqrrq,member_rhtzrq,member_yzzp,member_zjzp';
		$re = MemberModel::field($field)->findOrEmpty($id);
		if($re->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

        $res = $re;
        $member_id = $re->member_id;
        if (!empty($member_id)) {
            $member = Db::name('member')->where('member_id',$member_id)->find();

            $member_idx_arr = explode(',',$member['member_idx']);
            $fcxx_idx_arr = explode(',',$member['fcxx_idx']);

            $member_idx = Db::name('member')->where('member_id','in',$member_idx_arr)->column('member_name');

            //cewei
            $cewei_namex = Db::name('cewei')->alias('c')
                ->field('c.cewei_name,t.tccd_name,cw.cwqy_name')
                ->leftJoin('tccd t','t.tccd_id=c.tccd_id')
                ->leftJoin('cwqy cw','cw.cwqy_id=c.tccd_id')
                ->where('c.member_id',$member_id)->select();

            $cewei_namex_1 = [];
            foreach ($cewei_namex as $cewei_namex_item) {
                $cewei_namex_1[] = $cewei_namex_item['tccd_name'].'-'.$cewei_namex_item['cwqy_name'].'-'.$cewei_namex_item['cewei_name'];
            }

            $car_namex = Db::name('car')->where('member_id',$member_id)->column('car_name');

            // 房产
            $fcxx_fjbhx = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,d.louyu_name as danyaun_name,l.louyu_name')
                ->leftJoin('louyu d','f.louyu_id=d.louyu_id')
                ->leftJoin('louyu l','d.louyu_pid=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)->select();

            $fcxx_fjbhx_1 = [];
            foreach ($fcxx_fjbhx as $fcxx_fjbhx_item) {
                $fcxx_fjbhx_1[] = $fcxx_fjbhx_item['louyu_name'].'-'.$fcxx_fjbhx_item['danyaun_name'].'-'.$fcxx_fjbhx_item['fcxx_fjbh'];
            }

            // 门市
            $fcxx_ms = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,l.louyu_name as danyaun_name,l.louyu_name')
//                ->leftJoin('louyu d','f.louyu_id=d.louyu_id')
                ->leftJoin('louyu l','f.louyu_id=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)
                ->where('f.fwlx_id',2)
                ->whereNull('l.louyu_pid')->select();

            $fcxx_fjbhx_2 = [];
            foreach ($fcxx_ms as $fcxx_ms_item) {
                $fcxx_fjbhx_2[] = $fcxx_ms_item['louyu_name'].'-'.$fcxx_ms_item['danyaun_name'].'-'.$fcxx_ms_item['fcxx_fjbh'];
            }

            //车库
            $fcxx_ck = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,l.louyu_name as danyaun_name,l.louyu_name')
                ->leftJoin('louyu l','f.louyu_id=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)
                ->where('f.fwlx_id',4)
                ->whereNull('l.louyu_pid')->select();

            $fcxx_fjbhx_3 = [];
            foreach ($fcxx_ck as $fcxx_ck_item) {
                $fcxx_fjbhx_3[] = $fcxx_ck_item['louyu_name'].'-'.$fcxx_ck_item['danyaun_name'].'-'.$fcxx_ck_item['fcxx_fjbh'];
            }

            $res['member_name'] = $member['member_name'];
            $res['member_idx']  = implode(',',$member_idx);
            $res['fcxx_fjbhx']  = implode(',',$fcxx_fjbhx_1);
            $res['cewei_namex'] = implode(',',$cewei_namex_1);
            $res['fcxx_ms']     = implode(',',$fcxx_fjbhx_2);
            $res['fcxx_ck']     = implode(',',$fcxx_fjbhx_3);
            $res['car_namex']   = implode(',',$car_namex);

        }


		return json(['status'=>200,'data'=>$res]);
	}
	/*end*/

	/*
 	* @Description  关联车位
 	*/
	public function glchewei(){
		$postField = 'member_id,cewei_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Member::class);


		if(!isset($data['cewei_id'])){
			$data['cewei_id'] = null;
		}
		$data['cewei_id'] = implode(',',$data['cewei_id']);

		try{
			MemberModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Member@afterShopGlchewei',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getGlcheweiInfo(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,cewei_id';
		$res = MemberModel::field($field)->find($id);
		$res['cewei_id'] = explode(',',$res['cewei_id']);
		return json(['status'=>200,'data'=>$res]);
	}



	/*
 	* @Description  关联车辆
 	*/
	public function glcar(){
		$postField = 'member_id,car_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Member::class);


		if(!isset($data['car_id'])){
			$data['car_id'] = null;
		}
		$data['car_id'] = implode(',',$data['car_id']);

		try{
			MemberModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Member@afterShopGlcar',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getGlcarInfo(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,car_id';
		$res = MemberModel::field($field)->find($id);
		$res['car_id'] = explode(',',$res['car_id']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('zjlx_id,khlb_id,khlx_id,member_idx,cewei_id,car_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('zjlx_id',explode(',',$list))){
			$data['zjlx_ids'] = $this->query("select zjlx_id,zjlx_name from cd_zjlx",'mysql');
		}
		if(in_array('khlb_id',explode(',',$list))){
			$data['khlb_ids'] = $this->query("select khlb_id,khlb_name from cd_khlb",'mysql');
		}
		if(in_array('khlx_id',explode(',',$list))){
			$data['khlx_ids'] = $this->query("select khlx_id,khlx_name from cd_khlx",'mysql');
		}
		if(in_array('member_idx',explode(',',$list))){
			$data['member_idxs'] = _generateSelectTree($this->query(" select member_id as tval,concat_ws('-',member_name,member_tel) as tkey from cd_member where shop_id = ".session("shop.shop_id")." and xqgl_id = ".session("shop.xqgl_id")."",'mysql'));
		}
		if(in_array('cewei_id',explode(',',$list))){
			$data['cewei_ids'] = _generateSelectTree($this->query("select 
a.cewei_id as tval,
concat_ws('-',g.tccd_name,f.cwqy_name,a.cewei_name,z.member_name) as tkey
from cd_cewei as a 
left join cd_tccd as g on g.tccd_id = a.tccd_id 
left join cd_cwqy as f on f.cwqy_id = a.cwqy_id
left join cd_member as z on z.member_id = a.member_id
where a.xqgl_id=".session('shop.xqgl_id')."",'mysql'));
		}
		if(in_array('car_id',explode(',',$list))){
			$data['car_ids'] = _generateSelectTree($this->query("select 
a.car_id as tval,
concat_ws('-',a.car_name,z.member_name) as tkey 
from cd_car as a 
left join cd_member as z on z.member_id = a.member_id 
where a.xqgl_id = ".session("shop.xqgl_id")."",'mysql'));
		}
		return $data;
	}


/*start*/
	/*
 	* @Description  关联房产
 	*/
	public function glfangchan(){
		$postField = 'member_id,fcxx_idx';
		$data = $this->request->only(explode(',',$postField),'post',null);	
		$this->validate($data,\app\shop\validate\Member::class);

		//$data['fcxx_idx'] = implode(',',$data['fcxx_idx']);
		
		//去掉不是数字的数组开始
		$fcxx_idx = [];

        foreach ($data['fcxx_idx'] as $data_fcxx_idx) {

            if (is_numeric($data_fcxx_idx)) {

                $fcxx_idx[] = $data_fcxx_idx;

            }

        }

        $data['fcxx_idx'] = implode(',',$fcxx_idx);

        //去掉不是数字的数组结束
		try{
			MemberModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Member@afterShopGlfangchan',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}
	
	/*
 	* @Description  修改关联资产之前     查询信息的 勿要删除
 	*/
	function getGlfangchanInfo(){
		$id =  $this->request->post('member_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'member_id,fcxx_idx';
		$res = MemberModel::field($field)->find($id);		
		//$res['fcxx_idx'] = explode(',',$res['fcxx_idx']);
		
		//查询用户资产所在的楼和单元
		////////////////////////////////////////////////////////////////
		
		$fcxx_idx = explode(',',$res['fcxx_idx']);

        $danyuan = Db::name('fcxx')->alias('a')

            ->leftJoin('louyu b','a.louyu_id = b.louyu_id')

            ->whereNotNull('b.louyu_pid')

            ->wherein('a.fcxx_id',$fcxx_idx)->column('b.louyu_id');

        $louyu = Db::name('fcxx')->alias('a')

            ->leftJoin('louyu b','a.louyu_id = b.louyu_id')

            ->whereNull('b.louyu_pid')

            ->wherein('a.fcxx_id',$fcxx_idx)->column('b.louyu_id');

        $louyua= Db::name('louyu')->wherein('louyu_id',$danyuan)->column('louyu_pid');
//      $louyub= Db::name('louyu')->wherein('louyu_id',$danyuan)->column('louyu_id');

		$louyu = array_merge($louyua,$louyu);

        $louyu_L = $louyu;

        $danyuan_D = $danyuan;

        $louyu_C = $louyu;

        foreach ($louyu as $louyu_key => $louyu_item) {

            $louyu_C[$louyu_key] = 'C'.$louyu_item;

            $louyu_L[$louyu_key] = 'L'.$louyu_item;

        }

        foreach ($danyuan as $danyuan_key => $danyuan_item) {

            $danyuan_D[$danyuan_key] = 'D'.$danyuan_item;

        }
		

        $louyu_All = array_merge($louyu_L,$louyu_C);//数组合并
		
		$louyu_All = array_unique($louyu_All);//数组去重

        $fcxx_idx = array_merge(array_unique(array_merge($louyu_All,$danyuan_D)),$fcxx_idx);

		$res['fcxx_idx'] = $fcxx_idx;
		
		////////////////////////////////////////////////////////////
		
		
		return json(['status'=>200,'data'=>$res]);
	}



/*end*/


/*start*/
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
			->field('a.fcxx_id,a.fcxx_fjbh,a.louyu_id,b.member_name as kh_name')
			->leftJoin('member b','a.member_id = b.member_id')			
			->whereIn('a.louyu_id',$danyuan_ids)->select(); // 房产信息数组

        $cksf = Db::name('fcxx')->alias('a')
			->field('a.fcxx_id,a.fcxx_fjbh,a.louyu_id,b.member_name as kh_name')
			->leftJoin('member b','a.member_id = b.member_id')	
			->whereIn('louyu_id',$louyu_ids)->select(); // 车库商服信息数组

        $nodes = [];

        foreach ($louyu as $louyu_item) { //循环楼宇

            $danyuan_children = [];

            foreach ($danyuan as $danyuan_item) { //循环单元

                if ($louyu_item['louyu_id'] == $danyuan_item['louyu_pid']) {

                    $fcxx_children = [];

                    foreach ($fcxx as $fcxx_item) {//循环房间

                        if ($danyuan_item['louyu_id'] == $fcxx_item['louyu_id']) {

                            $fcxx_children[] = [

                                "access"    => $fcxx_item['fcxx_id'],

                                "title"     => $fcxx_item['fcxx_fjbh']."(-".$fcxx_item['kh_name']."-)",

                            ];

                        }

                    }

                    $danyuan_children[] = [

                        'access'    => 'D'.$danyuan_item['louyu_id'],

                        'title'     => $danyuan_item['louyu_name'],

                        'children'  => $fcxx_children

                    ];

                }

            }

            $cksf_children = [];

            foreach ($cksf as $cksf_item) {

                if ($louyu_item['louyu_id'] == $cksf_item['louyu_id']) {

                    $cksf_children[] = [

                        "access"    => intval($cksf_item['fcxx_id']),

                        "title"    => $cksf_item['fcxx_fjbh']."(-".$cksf_item['kh_name']."-)",

                    ];

                }

            }

            $danyuan_children[] = [

                'access'    => 'C'.$louyu_item['louyu_id'],

                'title'     => '车库商服',

                'children'  => $cksf_children

            ];

            $nodes[] = [

                'access'    => 'L'.$louyu_item['louyu_id'],

                'title'     => $louyu_item['louyu_name'],

                'children'  => $danyuan_children

            ];

        }
//dump($nodes);exit;
        return json(['status'=>200,'menus'=>$nodes]);

    }

    public function remoteFcxxidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "fcxx_fjbh like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'fcxx_id = '.$dataval;
        }
        $aaa = $this->query('select a.fcxx_id as tval, concat_ws("_",c.louyu_name,b.louyu_name,a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id left join cd_louyu c on b.louyu_pid = c.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is not null and '.$sqlstr,'mysql');
        $bbb = $this->query('select a.fcxx_id as tval, concat_ws("_",b.louyu_name,"商服/车库",a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is null and '.$sqlstr,'mysql');
        $data = _generateSelectTree(array_merge($aaa,$bbb));
        return json(['status'=>200,'data'=>$data]);
    }

    public function remoteCeweidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "cewei_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'cewei_id = '.$dataval;
        }
        $data = _generateSelectTree($this->query('select a.cewei_id as tval,concat_ws("_",g.tccd_name,f.cwqy_name,a.cewei_name) as tkey from cd_cewei as a left join cd_tccd as g on g.tccd_id = a.tccd_id left join cd_cwqy as f on f.cwqy_id = a.cwqy_id where a.xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));
//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id = '.session("shop.xqgl_id").' and '.$sqlstr,'mysql');
        return json(['status'=>200,'data'=>$data]);
    }

/*end*/
    public function rlsh(){
        $idx = $this->request->post('member_id', '', 'serach_in');
        if(empty($idx)) throw new ValidateException ('参数错误');

        $data['member_rzzt'] = 1;
        $res = MemberModel::where(['member_id'=>explode(',',$idx)])->update($data);
        return json(['status'=>200,'msg'=>'操作成功']);
    }
    public function htsh(){
        $idx = $this->request->post('member_id', '', 'serach_in');
        if(empty($idx)) throw new ValidateException ('参数错误');

        $data['member_rzzt'] = 0;
        $res = MemberModel::where(['member_id'=>explode(',',$idx)])->update($data);
        return json(['status'=>200,'msg'=>'操作成功']);
    }


}

