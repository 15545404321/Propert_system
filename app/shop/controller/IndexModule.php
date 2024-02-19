<?php 
/*
 module:		首页模块控制器
 create_time:	2023-05-05 16:47:06
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\IndexModule as IndexModuleModel;
use think\facade\Db;

class IndexModule extends Admin {


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
			$where['indexmodule_id'] = $this->request->post('indexmodule_id', '', 'serach_in');
			$where['indexmodule_name'] = $this->request->post('indexmodule_name', '', 'serach_in');
			$where['indexmodule_remarks'] = $this->request->post('indexmodule_remarks', '', 'serach_in');

			$field = 'indexmodule_id,indexmodule_name,indexmodule_remarks';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'indexmodule_id desc';

			$query = IndexModuleModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'indexmodule_name,indexmodule_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\IndexModule::class);

		try{
			$res = IndexModuleModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'indexmodule_id,indexmodule_name,indexmodule_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\IndexModule::class);

		try{
			IndexModuleModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('indexmodule_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'indexmodule_id,indexmodule_name,indexmodule_remarks';
		$res = IndexModuleModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('indexmodule_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		IndexModuleModel::destroy(['indexmodule_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('indexmodule_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'indexmodule_id,indexmodule_name,indexmodule_remarks';
		$res = IndexModuleModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

