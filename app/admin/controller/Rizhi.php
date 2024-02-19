<?php 
/*
 module:		开通日志方法控制器
 create_time:	2023-01-26 10:58:48
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Rizhi as RizhiModel;
use think\facade\Db;

class Rizhi extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','detail'])){
			$idx = $this->request->post('rizhi_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = RizhiModel::find($v);
					if(session('admin.role_id') <> 1 && $info['user_id'] <> session('admin.user_id')){
						throw new ValidateException('你没有操作权限');
					}
				}
			}
		}
	}


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
			$where['rizhi_id'] = $this->request->post('rizhi_id', '', 'serach_in');
			$where['rz_gongneng'] = $this->request->post('rz_gongneng', '', 'serach_in');
			$where['rz_fangfa'] = $this->request->post('rz_fangfa', '', 'serach_in');
			$where['rz_status'] = $this->request->post('rz_status', '', 'serach_in');

			if(!in_array(session('admin.role_id'),[1])){
				$where['user_id'] = session('admin.user_id');
			}

			$field = 'rizhi_id,rz_gongneng,rz_fangfa,rz_status,rz_time,user_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'rizhi_id desc';

			$query = RizhiModel::field($field);

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
		$postField = 'rizhi_id,rz_status';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['rizhi_id']) throw new ValidateException ('参数错误');
		RizhiModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'rz_gongneng,rz_fangfa,rz_status,rz_time,user_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Rizhi::class);

		$data['rz_time'] = time();
		$data['user_id'] = session('admin.user_id');

		try{
			$res = RizhiModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'rizhi_id,rz_gongneng,rz_fangfa,rz_status,rz_time,user_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Rizhi::class);

		$data['rz_time'] = !empty($data['rz_time']) ? strtotime($data['rz_time']) : '';
		$data['user_id'] = session('admin.user_id');

		try{
			RizhiModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('rizhi_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'rizhi_id,rz_gongneng,rz_fangfa,rz_status,rz_time,user_id';
		$res = RizhiModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('rizhi_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'rizhi_id,rz_gongneng,rz_fangfa,rz_status,rz_time,user_id';
		$res = RizhiModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

