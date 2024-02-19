<?php 
/*
 module:		楼宇类型控制器
 create_time:	2022-12-08 14:17:34
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\LouyuType as LouyuTypeModel;
use think\facade\Db;

class LouyuType extends Admin {


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
			$where['louyutype_id'] = $this->request->post('louyutype_id', '', 'serach_in');
			$where['louyutype_name'] = $this->request->post('louyutype_name', '', 'serach_in');

			$field = 'louyutype_id,louyutype_name';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'louyutype_id desc';

			$query = LouyuTypeModel::field($field);

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
		$postField = 'louyutype_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['louyutype_id']) throw new ValidateException ('参数错误');
		LouyuTypeModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'louyutype_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\LouyuType::class);

		try{
			$res = LouyuTypeModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'louyutype_id,louyutype_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\LouyuType::class);

		try{
			LouyuTypeModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('louyutype_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'louyutype_id,louyutype_name';
		$res = LouyuTypeModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('louyutype_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		LouyuTypeModel::destroy(['louyutype_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('louyutype_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'louyutype_id,louyutype_name';
		$res = LouyuTypeModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

