<?php 
/*
 module:		资产类别控制器
 create_time:	2022-12-29 16:37:08
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zclb as ZclbModel;
use think\facade\Db;

class Zclb extends Admin {


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
			$where['zclb_id'] = $this->request->post('zclb_id', '', 'serach_in');
			$where['zclb_name'] = $this->request->post('zclb_name', '', 'serach_in');
			$where['zclb_fid'] = $this->request->post('zclb_fid', '', 'serach_in');

			$field = 'zclb_id,zclb_name,zclb_fid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zclb_id desc';

			$query = ZclbModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['zclb_id','zclb_fid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('zclb_fid');
			return json($data);
		}
	}


	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'zclb_name,zclb_fid';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zclb::class);

		try{
			$res = ZclbModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'zclb_id,zclb_name,zclb_fid';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zclb::class);


		if(!isset($data['zclb_fid'])){
			$data['zclb_fid'] = null;
		}

		try{
			ZclbModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zclb_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zclb_id,zclb_name,zclb_fid';
		$res = ZclbModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zclb_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ZclbModel::destroy(['zclb_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('zclb_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zclb_id,zclb_name';
		$res = ZclbModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('zclb_fid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('zclb_fid',explode(',',$list))){
			$data['zclb_fids'] = _generateSelectTree($this->query("select zclb_id,zclb_name,zclb_fid from cd_zclb",'mysql'));
		}
		return $data;
	}



}

