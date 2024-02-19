<?php 
/*
 module:		票据类型控制器
 create_time:	2023-01-18 11:16:37
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Pjlx as PjlxModel;
use think\facade\Db;

class Pjlx extends Admin {


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
			$where['pjlx_id'] = $this->request->post('pjlx_id', '', 'serach_in');
			$where['pjlx_name'] = $this->request->post('pjlx_name', '', 'serach_in');
			$where['pjlx_pid'] = $this->request->post('pjlx_pid', '', 'serach_in');
			$where['pjlx_wenjian'] = $this->request->post('pjlx_wenjian', '', 'serach_in');

			$field = 'pjlx_id,pjlx_name,pjlx_wenjian,pjlx_pid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'pjlx_id desc';

			$query = PjlxModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['pjlx_id','pjlx_pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('pjlx_pid');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'pjlx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['pjlx_id']) throw new ValidateException ('参数错误');
		PjlxModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'pjlx_name,pjlx_pid,pjlx_wenjian';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Pjlx::class);

		try{
			$res = PjlxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'pjlx_id,pjlx_name,pjlx_pid,pjlx_wenjian';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Pjlx::class);


		if(!isset($data['pjlx_pid'])){
			$data['pjlx_pid'] = null;
		}

		try{
			PjlxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('pjlx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjlx_id,pjlx_name,pjlx_pid,pjlx_wenjian';
		$res = PjlxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('pjlx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		PjlxModel::destroy(['pjlx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('pjlx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjlx_id,pjlx_name,pjlx_wenjian';
		$res = PjlxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('pjlx_pid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('pjlx_pid',explode(',',$list))){
			$data['pjlx_pids'] = _generateSelectTree($this->query("select pjlx_id,pjlx_name,pjlx_pid from cd_pjlx",'mysql'));
		}
		return $data;
	}



}

