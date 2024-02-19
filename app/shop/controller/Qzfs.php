<?php 
/*
 module:		取整方式控制器
 create_time:	2022-12-14 14:17:34
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Qzfs as QzfsModel;
use think\facade\Db;

class Qzfs extends Admin {


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
			$where['qzfs_id'] = $this->request->post('qzfs_id', '', 'serach_in');
			$where['qzfs_name'] = $this->request->post('qzfs_name', '', 'serach_in');
			$where['qzfs_gnms'] = $this->request->post('qzfs_gnms', '', 'serach_in');

			$field = 'qzfs_id,qzfs_name,qzfs_gnms';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'qzfs_id desc';

			$query = QzfsModel::field($field);

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
		$postField = 'qzfs_name,qzfs_gnms';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Qzfs::class);

		try{
			$res = QzfsModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'qzfs_id,qzfs_name,qzfs_gnms';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Qzfs::class);

		try{
			QzfsModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('qzfs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'qzfs_id,qzfs_name,qzfs_gnms';
		$res = QzfsModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('qzfs_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		QzfsModel::destroy(['qzfs_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('qzfs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'qzfs_id,qzfs_name,qzfs_gnms';
		$res = QzfsModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

