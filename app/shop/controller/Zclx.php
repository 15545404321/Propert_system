<?php 
/*
 module:		资产类型控制器
 create_time:	2022-12-17 09:51:42
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zclx as ZclxModel;
use think\facade\Db;

class Zclx extends Admin {


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
			$where['zclx_id'] = $this->request->post('zclx_id', '', 'serach_in');
			$where['zclx_name'] = $this->request->post('zclx_name', '', 'serach_in');

			$field = 'zclx_id,zclx_name';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zclx_id desc';

			$query = ZclxModel::field($field);

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
		$postField = 'zclx_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zclx::class);

		try{
			$res = ZclxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'zclx_id,zclx_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zclx::class);

		try{
			ZclxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zclx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zclx_id,zclx_name';
		$res = ZclxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zclx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ZclxModel::destroy(['zclx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('zclx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zclx_id,zclx_name';
		$res = ZclxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

