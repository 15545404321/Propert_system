<?php 
/*
 module:		车位信息控制器
 create_time:	2023-03-13 11:10:39
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Cewei as CeweiModel;
use think\facade\Db;

class Cewei extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','zcgl','getZcglInfo'])){
			$idx = $this->request->post('cewei_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = CeweiModel::find($v);
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
			$where['cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');

			$where['cewei.shop_id'] = session('shop.shop_id');

			$where['cewei.xqgl_id'] = session('shop.xqgl_id');
			$where['cewei.cewei_name'] = $this->request->post('cewei_name', '', 'serach_in');
			$where['cewei.tccd_id'] = $this->request->post('tccd_id', '', 'serach_in');
			$where['cewei.cwqy_id'] = $this->request->post('cwqy_id', '', 'serach_in');
			$where['cewei.cwzt_id'] = $this->request->post('cwzt_id', '', 'serach_in');
			$where['cewei.cewei_zcbh'] = $this->request->post('cewei_zcbh', '', 'serach_in');
			$where['cewei.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['cewei.cewei_remarks'] = $this->request->post('cewei_remarks', '', 'serach_in');
			$where['cewei.cwlx_id'] = $this->request->post('cwlx_id', '', 'serach_in');

			$field = 'cewei_id,cewei_name,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cewei_start_time,cewei_end_time,cewei_zcbh,member_id,cwlx_id,px';

			$withJoin = [
				'cwlx'=>explode(',','cwlx_name'),
				'tccd'=>explode(',','tccd_name'),
				'cwqy'=>explode(',','cwqy_name'),
				'cwzt'=>explode(',','cwzt_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'px asc';

			$query = CeweiModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['tccd_id']){
					$res['data'][$k]['tccd_id'] = Db::query("select tccd_name from  cd_tccd where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and tccd_id=".$v['tccd_id']."")[0]['tccd_name'];
				}
				if($v['cwqy_id']){
					$res['data'][$k]['cwqy_id'] = Db::query("select cwqy_name from  cd_cwqy where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and cwqy_id=".$v['cwqy_id']."")[0]['cwqy_name'];
				}
				if($v['cwzt_id']){
					$res['data'][$k]['cwzt_id'] = Db::query("select cwzt_name from  cd_cwzt where cwzt_id=".$v['cwzt_id']."")[0]['cwzt_name'];
				}
				if($v['member_id']){
					$res['data'][$k]['member_id'] = Db::query("select member_name from  cd_member where xqgl_id = ".session("shop.xqgl_id")." and member_id=".$v['member_id']."")[0]['member_name'];
				}
				if($v['cwlx_id']){
					$res['data'][$k]['cwlx_id'] = Db::query("select cwlx_name from  cd_cwlx where cwlx_id=".$v['cwlx_id']."")[0]['cwlx_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('tccd_id,cwqy_id,cwzt_id,cwlx_id');
			return json($data);
		}
	}

	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'cewei_id,px';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['cewei_id']) throw new ValidateException ('参数错误');
		CeweiModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,cewei_name,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cewei_start_time,cewei_end_time,cewei_zcbh,member_id,cewei_remarks,cw_pltj,cw_num,cwlx_id,px';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cewei::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['cewei_start_time'] = !empty($data['cewei_start_time']) ? strtotime($data['cewei_start_time']) : '';
		$data['cewei_end_time'] = !empty($data['cewei_end_time']) ? strtotime($data['cewei_end_time']) : '';

		try{
			$res = CeweiModel::insertGetId($data);
			if($res && empty($data['px'])){
				CeweiModel::update(['px'=>$res,'cewei_id'=>$res]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Cewei@afterShopAdd',array_merge($data,['cewei_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'cewei_id,shop_id,xqgl_id,cewei_name,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cewei_start_time,cewei_end_time,cewei_zcbh,member_id,cewei_remarks,cwlx_id,px';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cewei::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['cewei_start_time'] = !empty($data['cewei_start_time']) ? strtotime($data['cewei_start_time']) : '';
		$data['cewei_end_time'] = !empty($data['cewei_end_time']) ? strtotime($data['cewei_end_time']) : '';

		try{
			CeweiModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('cewei_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cewei_id,shop_id,xqgl_id,cewei_name,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cewei_start_time,cewei_end_time,cewei_zcbh,member_id,cewei_remarks,cwlx_id,px';
		$res = CeweiModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('cewei_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Cewei@beforShopDelete',$idx)){
			return $ret;
		}

		CeweiModel::destroy(['cewei_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  资产关联
 	*/
	public function zcgl(){
		$postField = 'cewei_id,member_id,cwzt_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cewei::class);

        if($ret = hook('hook/Cewei@beforShopZcgl',$data)){
            return $ret;
        }

		try{
			CeweiModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Cewei@afterShopZcgl',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getZcglInfo(){
		$id =  $this->request->post('cewei_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cewei_id,member_id,cwzt_id';
		$res = CeweiModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
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
			$list[$key]['cewei_name'] = $val['车位编号'];
			$list[$key]['tccd_id'] = $val['停车场地'];
			$list[$key]['cwqy_id'] = $val['车位区域'];
			$list[$key]['cwzt_id'] = $val['车位状态'];
			$list[$key]['cewei_cwmj'] = $val['车位面积'];
			$list[$key]['cewei_start_time'] = strtotime($val['开始日期']);
			$list[$key]['cewei_end_time'] = strtotime($val['结束日期']);
			$list[$key]['cewei_zcbh'] = $val['资产编号'];
			$list[$key]['member_id'] = $val['产权所属'];
			$list[$key]['cewei_remarks'] = $val['备注信息'];
			$list[$key]['cw_pltj'] = getValByKey($val['批量添加'],'[{"key":"单条添加","val":"1","label_color":"primary"},{"key":"批量添加","val":"2","label_color":"danger"}]');
			$list[$key]['cw_num'] = $val['新增数量'];
			$list[$key]['cwlx_id'] = $val['车位类型'];
			$list[$key]['px'] = $val['排序'];
		}
		(new CeweiModel)->insertAll($list);
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
		$where['cewei_id'] = ['in',$this->request->param('cewei_id', '', 'serach_in')];

		$where['shop_id'] = session('shop.shop_id');

		$where['xqgl_id'] = session('shop.xqgl_id');
		$where['cewei_name'] = $this->request->param('cewei_name', '', 'serach_in');
		$where['tccd_id'] = $this->request->param('tccd_id', '', 'serach_in');
		$where['cwqy_id'] = $this->request->param('cwqy_id', '', 'serach_in');
		$where['cwzt_id'] = $this->request->param('cwzt_id', '', 'serach_in');
		$where['cewei_zcbh'] = $this->request->param('cewei_zcbh', '', 'serach_in');
		$where['member_id'] = $this->request->param('member_id', '', 'serach_in');
		$where['cewei_remarks'] = $this->request->param('cewei_remarks', '', 'serach_in');
		$where['cwlx_id'] = $this->request->param('cwlx_id', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'cewei_id desc';

		$field = 'shop_id,xqgl_id,cewei_name,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cewei_start_time,cewei_end_time,cewei_zcbh,member_id,cewei_remarks,cw_pltj,cw_num,cwlx_id,px';

		$res = CeweiModel::where(formatWhere($where))->field($field)->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		foreach($res['data'] as $key=>$val){
			$res['data'][$key]['cewei_start_time'] = !empty($val['cewei_start_time']) ? date('Y-m-d',$val['cewei_start_time']) : '';
			$res['data'][$key]['cewei_end_time'] = !empty($val['cewei_end_time']) ? date('Y-m-d',$val['cewei_end_time']) : '';
			$res['data'][$key]['cw_pltj'] = getItemVal($val['cw_pltj'],'[{"key":"单条添加","val":"1","label_color":"primary"},{"key":"批量添加","val":"2","label_color":"danger"}]');
			unset($res['data'][$key]['cewei_id']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','所属物业,所属小区,车位编号,停车场地,车位区域,车位状态,车位面积,开始日期,结束日期,资产编号,产权所属,备注信息,批量添加,新增数量,车位类型,排序');
		$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
		$data['filename'] = '车位信息.'.config('my.dump_extension');
		$data['data'] = $res['data'];
		return json($data);
	}


	/*
 	* @Description  更改属性
 	*/
	public function sxupdate(){
		$postField = 'cewei_id,tccd_id,cwqy_id,cwzt_id,cewei_cwmj,cwlx_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cewei::class);

		$idx = explode(',',$data['cewei_id']);
		unset($data['cewei_id']);

		try{
			CeweiModel::where(['cewei_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('tccd_id,cwqy_id,cwzt_id,cwlx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('tccd_id',explode(',',$list))){
			$data['tccd_ids'] = $this->query("select tccd_id,tccd_name from cd_tccd where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")."",'mysql');
		}
		if(in_array('cwqy_id',explode(',',$list))){
			$data['cwqy_ids'] = $this->query("select cwqy_id,cwqy_name from cd_cwqy where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")."",'mysql');
		}
		if(in_array('cwzt_id',explode(',',$list))){
			$data['cwzt_ids'] = $this->query("select cwzt_id,cwzt_name from cd_cwzt",'mysql');
		}
		if(in_array('cwlx_id',explode(',',$list))){
			$data['cwlx_ids'] = $this->query("select cwlx_id,cwlx_name from cd_cwlx",'mysql');
		}
		return $data;
	}


/*start*/
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
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));
//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id = '.session("shop.xqgl_id").' and '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}
/*end*/


/*start*/
	/*
 	* @Description  批量添加
 	*/
	public function batchAdd(){
		
		$data = $this->request->post($data);	

        foreach ($data['data'] as $data_key => $data_item) {
            $data['data'][$data_key]['shop_id'] = session('shop.shop_id');
            $data['data'][$data_key]['xqgl_id'] = session('shop.xqgl_id');
        }
        
		(new CeweiModel)->saveAll($data['data']);
		return json(['status'=>200,'msg'=>'添加成功']);
	}
/*end*/



}

