<?php 
/*
 module:		公共权限控制器
 create_time:	2023-01-20 16:14:40
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Ggqx as GgqxModel;
use think\facade\Db;

class Ggqx extends Admin {


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
			$where['ggqx_id'] = $this->request->post('ggqx_id', '', 'serach_in');
			$where['ggqx_name'] = $this->request->post('ggqx_name', '', 'serach_in');
			$where['ggqx_url'] = $this->request->post('ggqx_url', '', 'serach_in');
			$where['ggqx_kfry'] = $this->request->post('ggqx_kfry', '', 'serach_in');
			$where['ggqx_beizhu'] = $this->request->post('ggqx_beizhu', '', 'serach_in');

			$field = 'ggqx_id,ggqx_name,ggqx_url,ggqx_kfry,ggqx_beizhu,ggqx_time';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ggqx_id desc';

			$query = GgqxModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'ggqx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['ggqx_id']) throw new ValidateException ('参数错误');
		GgqxModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'ggqx_name,ggqx_url,ggqx_kfry,ggqx_beizhu,ggqx_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Ggqx::class);

		$data['ggqx_time'] = time();

		try{
			$res = GgqxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'ggqx_id,ggqx_name,ggqx_url,ggqx_kfry,ggqx_beizhu,ggqx_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Ggqx::class);

		$data['ggqx_time'] = !empty($data['ggqx_time']) ? strtotime($data['ggqx_time']) : '';

		try{
			GgqxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('ggqx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ggqx_id,ggqx_name,ggqx_url,ggqx_kfry,ggqx_beizhu,ggqx_time';
		$res = GgqxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('ggqx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		GgqxModel::destroy(['ggqx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('ggqx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ggqx_id,ggqx_name,ggqx_url,ggqx_kfry,ggqx_beizhu,ggqx_time';
		$res = GgqxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

