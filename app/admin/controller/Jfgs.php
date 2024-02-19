<?php 
/*
 module:		计费公式控制器
 create_time:	2022-12-29 09:14:00
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Jfgs as JfgsModel;
use think\facade\Db;

class Jfgs extends Admin {


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
			$where['jfgs_id'] = $this->request->post('jfgs_id', '', 'serach_in');
			$where['jfgs_name'] = $this->request->post('jfgs_name', '', 'serach_in');
			$where['fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');

			$field = 'jfgs_id,jfgs_name,fylx_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'jfgs_id asc';

			$query = JfgsModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['fylx_id']){
					$res['data'][$k]['fylx_id'] = Db::query("select fylx_name from  cd_fylx where fylx_id=".$v['fylx_id']."")[0]['fylx_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fylx_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'jfgs_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['jfgs_id']) throw new ValidateException ('参数错误');
		JfgsModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'jfgs_name,fylx_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Jfgs::class);

		try{
			$res = JfgsModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'jfgs_id,jfgs_name,fylx_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Jfgs::class);

		try{
			JfgsModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('jfgs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'jfgs_id,jfgs_name,fylx_id';
		$res = JfgsModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('jfgs_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		JfgsModel::destroy(['jfgs_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('jfgs_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'jfgs_id,jfgs_name,fylx_id';
		$res = JfgsModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fylx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		return $data;
	}



}

