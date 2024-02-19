<?php 
/*
 module:		收款方式控制器
 create_time:	2022-12-31 14:33:54
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Skfs as SkfsModel;
use think\facade\Db;

class Skfs extends Admin {


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
			$where['skfs_id'] = $this->request->post('skfs_id', '', 'serach_in');
			$where['skfs_name'] = $this->request->post('skfs_name', '', 'serach_in');

			$field = 'skfs_id,skfs_name';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'skfs_id desc';

			$query = SkfsModel::field($field);

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
		$postField = 'skfs_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['skfs_id']) throw new ValidateException ('参数错误');
		SkfsModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'skfs_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Skfs::class);

		try{
			$res = SkfsModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'skfs_id,skfs_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Skfs::class);

		try{
			SkfsModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('skfs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'skfs_id,skfs_name';
		$res = SkfsModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('skfs_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		SkfsModel::destroy(['skfs_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('skfs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'skfs_id,skfs_name';
		$res = SkfsModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

