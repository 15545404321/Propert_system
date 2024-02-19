<?php 
/*
 module:		单元管理控制器
 create_time:	2023-03-14 18:37:59
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Danyuan as DanyuanModel;
use think\facade\Db;

class Danyuan extends Admin {


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
			$where['louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['louyu_pid'] = $this->request->post('louyu_pid', '', 'serach_in');

			$field = 'louyu_id,louyu_name,louyu_pid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'louyu_id asc';

			$query = DanyuanModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['louyu_id','louyu_pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_pid');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'louyu_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['louyu_id']) throw new ValidateException ('参数错误');
		DanyuanModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,louyu_pid,louyu_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Danyuan::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = DanyuanModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'louyu_id,shop_id,xqgl_id,louyu_pid,louyu_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Danyuan::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			DanyuanModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('louyu_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'louyu_id,shop_id,xqgl_id,louyu_pid,louyu_name';
		$res = DanyuanModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('louyu_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		DanyuanModel::destroy(['louyu_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('louyu_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'louyu_id,louyu_name';
		$res = DanyuanModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('louyu_pid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('louyu_pid',explode(',',$list))){
			$data['louyu_pids'] = $this->query("select louyu_id,louyu_name from cd_louyu where louyu_pid is null",'mysql');
		}
		return $data;
	}



}

