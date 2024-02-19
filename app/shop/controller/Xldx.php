<?php 
/*
 module:		下拉多选控制器
 create_time:	2023-03-17 11:54:31
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Xldx as XldxModel;
use think\facade\Db;

class Xldx extends Admin {


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
			$where['xldx_id'] = $this->request->post('xldx_id', '', 'serach_in');
			$where['member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
			$where['fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['cwfy_scfs'] = $this->request->post('cwfy_scfs', '', 'serach_in');
			$where['cwfy_sclx'] = $this->request->post('cwfy_sclx', '', 'serach_in');

			$cwfy_kstime = $this->request->post('cwfy_kstime', '', 'serach_in');
			$where['cwfy_kstime'] = ['between',[strtotime($cwfy_kstime[0]),strtotime($cwfy_kstime[1])]];

			$cwfy_zztime = $this->request->post('cwfy_zztime', '', 'serach_in');
			$where['cwfy_zztime'] = ['between',[strtotime($cwfy_zztime[0]),strtotime($cwfy_zztime[1])]];
			$where['dxxx'] = ['find in set',$this->request->post('dxxx', '', 'serach_in')];
			$where['duoxuan'] = ['find in set',$this->request->post('duoxuan', '', 'serach_in')];

			$field = 'xldx_id,cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id,dxxx,duoxuan,xldx_wenjian';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'xldx_id desc';

			$query = XldxModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fydy_id,duoxuan');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFybz_id(){
		$fydy_id =  $this->request->post('fydy_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fybz_id,fybz_name from cd_fybz where xqgl_id='.session('shop.xqgl_id').' and fydy_id ='.$fydy_id,'mysql');
		return json($data);
	}


	/*
	* @Description  获取远程搜索字段信息
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
		$data = $this->query('select cewei_id,cewei_name from cd_cewei where xqgl_id=".session("shop.xqgl_id")." where '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'xldx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['xldx_id']) throw new ValidateException ('参数错误');
		XldxModel::update($data);

		if($ret = hook('hook/Xldx@afterShopUpdateExt',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id,dxxx,duoxuan,xldx_wenjian';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xldx::class);

		$data['cwfy_kstime'] = !empty($data['cwfy_kstime']) ? strtotime($data['cwfy_kstime']) : '';
		$data['cwfy_zztime'] = !empty($data['cwfy_zztime']) ? strtotime($data['cwfy_zztime']) : '';
		$data['dxxx'] = implode(',',$data['dxxx']);
		$data['duoxuan'] = implode(',',$data['duoxuan']);

		try{
			$res = XldxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'xldx_id,cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id,dxxx,duoxuan,xldx_wenjian';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xldx::class);

		$data['cwfy_kstime'] = !empty($data['cwfy_kstime']) ? strtotime($data['cwfy_kstime']) : '';
		$data['cwfy_zztime'] = !empty($data['cwfy_zztime']) ? strtotime($data['cwfy_zztime']) : '';
		$data['dxxx'] = implode(',',$data['dxxx']);
		$data['duoxuan'] = implode(',',$data['duoxuan']);

		try{
			XldxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('xldx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xldx_id,cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id,dxxx,duoxuan,xldx_wenjian';
		$res = XldxModel::field($field)->find($id);
		$res['dxxx'] = explode(',',$res['dxxx']);
		$res['duoxuan'] = explode(',',$res['duoxuan']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('xldx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		XldxModel::destroy(['xldx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('xldx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xldx_id,cewei_id,fydy_id,fybz_id,cwfy_scfs,cwfy_sclx,cwfy_kstime,cwfy_zztime,cwfy_ksmonth,cwfy_zzmonth,member_id,dxxx,duoxuan,xldx_wenjian';
		$res = XldxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  复制单条数据
 	*/
	public function copydata(){
		$postField = 'xldx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xldx::class);

		$data['cwfy_kstime'] = !empty($data['cwfy_kstime']) ? strtotime($data['cwfy_kstime']) : '';
		$data['cwfy_zztime'] = !empty($data['cwfy_zztime']) ? strtotime($data['cwfy_zztime']) : '';
		$data['dxxx'] = implode(',',$data['dxxx']);
		$data['duoxuan'] = implode(',',$data['duoxuan']);

		unset($data['xldx_id']);

		try{
			$res = XldxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getCopydataInfo(){
		$id =  $this->request->post('xldx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xldx_id,';
		$res = XldxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  添加文件
 	*/
	public function addWenjian(){
		$postField = 'xldx_wenjian';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xldx::class);

		try{
			$res = XldxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fydy_id,duoxuan')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fydy_id',explode(',',$list))){
			$data['fydy_ids'] = $this->query("select fydy_id,fydy_name from cd_fydy where xqgl_id=".session("shop.xqgl_id")." and fylb_id=5 and fylx_id=1",'mysql');
		}
		if(in_array('duoxuan',explode(',',$list))){
			$data['duoxuans'] = $this->query("select fybz_id,fybz_name from cd_fybz where xqgl_id=".sesion("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}



}

