<?php 
/*
 module:		Qcs控制器
 create_time:	2023-05-08 16:06:09
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Qcs as QcsModel;
use think\facade\Db;

class Qcs extends Admin {


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
			$where['qcs_id'] = $this->request->post('qcs_id', '', 'serach_in');
			$where['status'] = $this->request->post('status', '', 'serach_in');

			$field = 'qcs_id,qcs_jwd,status';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'qcs_id desc';

			$query = QcsModel::field($field);

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
		$postField = 'qcs_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['qcs_id']) throw new ValidateException ('参数错误');
		QcsModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'qcs_jwd,status';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Qcs::class);

		try{
			$res = QcsModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'qcs_id,qcs_jwd,status';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Qcs::class);

		try{
			QcsModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('qcs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'qcs_id,qcs_jwd,status';
		$res = QcsModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('qcs_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		QcsModel::destroy(['qcs_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('qcs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'qcs_id,qcs_jwd,status';
		$res = QcsModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  修改状态
 	*/
	public function updateStatus(){
		$idx = $this->request->post('qcs_id', '', 'serach_in');
		if(empty($idx)) throw new ValidateException ('参数错误');

		$data['status'] = 1;
		$res = QcsModel::where(['qcs_id'=>explode(',',$idx)])->update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}




}

