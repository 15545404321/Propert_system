<?php 
/*
 module:		费用单位控制器
 create_time:	2022-12-13 11:53:16
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Fydw as FydwModel;
use think\facade\Db;

class Fydw extends Admin {


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
			$where['fydw_id'] = $this->request->post('fydw_id', '', 'serach_in');
			$where['fydw_name'] = $this->request->post('fydw_name', '', 'serach_in');

			$field = 'fydw_id,fydw_name';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fydw_id desc';

			$query = FydwModel::field($field);

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
		$postField = 'fydw_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['fydw_id']) throw new ValidateException ('参数错误');
		FydwModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'fydw_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Fydw::class);

		try{
			$res = FydwModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'fydw_id,fydw_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Fydw::class);

		try{
			FydwModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('fydw_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fydw_id,fydw_name';
		$res = FydwModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('fydw_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		FydwModel::destroy(['fydw_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('fydw_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fydw_id,fydw_name';
		$res = FydwModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

