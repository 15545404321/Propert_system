<?php 
/*
 module:		装修配置控制器
 create_time:	2023-05-05 14:18:09
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\DecoConfig as DecoConfigModel;
use think\facade\Db;

class DecoConfig extends Admin {


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
			$where['decoconfig_id'] = $this->request->post('decoconfig_id', '', 'serach_in');
			$where['decoconfig_name'] = $this->request->post('decoconfig_name', '', 'serach_in');
			$where['decoconfig_url'] = $this->request->post('decoconfig_url', '', 'serach_in');
			$where['decoconfig_type'] = $this->request->post('decoconfig_type', '', 'serach_in');
			$where['decoconfig_remark'] = $this->request->post('decoconfig_remark', '', 'serach_in');

			$field = 'decoconfig_id,decoconfig_name,decoconfig_url,decoconfig_type,decoconfig_remark';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'decoconfig_id desc';

			$query = DecoConfigModel::field($field);

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
		$postField = 'decoconfig_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['decoconfig_id']) throw new ValidateException ('参数错误');
		DecoConfigModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'decoconfig_name,decoconfig_url,decoconfig_type,decoconfig_remark';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\DecoConfig::class);

		try{
			$res = DecoConfigModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'decoconfig_id,decoconfig_name,decoconfig_url,decoconfig_type,decoconfig_remark';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\DecoConfig::class);

		try{
			DecoConfigModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('decoconfig_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'decoconfig_id,decoconfig_name,decoconfig_url,decoconfig_type,decoconfig_remark';
		$res = DecoConfigModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('decoconfig_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		DecoConfigModel::destroy(['decoconfig_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('decoconfig_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'decoconfig_id,decoconfig_name,decoconfig_url,decoconfig_type,decoconfig_remark';
		$res = DecoConfigModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

