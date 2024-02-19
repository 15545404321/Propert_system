<?php 
/*
 module:		仪表种类控制器
 create_time:	2022-12-12 10:17:59
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Ybzl as YbzlModel;
use think\facade\Db;

class Ybzl extends Admin {


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
			$where['ybzl_id'] = $this->request->post('ybzl_id', '', 'serach_in');
			$where['ybzl_name'] = $this->request->post('ybzl_name', '', 'serach_in');

			$field = 'ybzl_id,ybzl_name,ybzl_px,ybzl_pid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ybzl_id desc';

			$query = YbzlModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['ybzl_id','ybzl_pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('ybzl_pid');
			return json($data);
		}
	}


	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'ybzl_name,ybzl_px,ybzl_pid';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ybzl::class);

		try{
			$res = YbzlModel::insertGetId($data);
			if($res && empty($data['ybzl_px'])){
				YbzlModel::update(['ybzl_px'=>$res,'ybzl_id'=>$res]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'ybzl_id,ybzl_name,ybzl_px,ybzl_pid';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ybzl::class);


		if(!isset($data['ybzl_pid'])){
			$data['ybzl_pid'] = null;
		}

		try{
			YbzlModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('ybzl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ybzl_id,ybzl_name,ybzl_px,ybzl_pid';
		$res = YbzlModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('ybzl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		YbzlModel::destroy(['ybzl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('ybzl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ybzl_id,ybzl_name,ybzl_px';
		$res = YbzlModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('ybzl_pid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('ybzl_pid',explode(',',$list))){
			$data['ybzl_pids'] = _generateSelectTree($this->query("select ybzl_id,ybzl_name,ybzl_pid from cd_ybzl",'mysql'));
		}
		return $data;
	}



}

