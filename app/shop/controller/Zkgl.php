<?php 
/*
 module:		折扣管理控制器
 create_time:	2022-12-19 00:06:14
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zkgl as ZkglModel;
use think\facade\Db;

class Zkgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('zkgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = ZkglModel::find($v);
					if($info['shop_admin_id'] <> session('shop.shop_admin_id')){
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
			$where['zkgl_id'] = $this->request->post('zkgl_id', '', 'serach_in');
			$where['zkgl.zkgl_name'] = $this->request->post('zkgl_name', '', 'serach_in');
			$where['zkgl.zkgl_zks'] = $this->request->post('zkgl_zks', '', 'serach_in');

			$where['zkgl.shop_admin_id'] = session('shop.shop_admin_id');
			$where['zkgl.zkgl_remarks'] = $this->request->post('zkgl_remarks', '', 'serach_in');

			$field = 'zkgl_id,zkgl_name,zkgl_zks,zkgl_addtime,zkgl_remarks';

			$withJoin = [
				'shopadmin'=>explode(',','cname'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zkgl_id desc';

			$query = ZkglModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'zkgl_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['zkgl_id']) throw new ValidateException ('参数错误');
		ZkglModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'zkgl_name,zkgl_zks,shop_admin_id,zkgl_addtime,zkgl_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zkgl::class);

		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['zkgl_addtime'] = time();

		try{
			$res = ZkglModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'zkgl_id,zkgl_name,zkgl_zks,shop_admin_id,zkgl_addtime,zkgl_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zkgl::class);

		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['zkgl_addtime'] = !empty($data['zkgl_addtime']) ? strtotime($data['zkgl_addtime']) : '';

		try{
			ZkglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zkgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zkgl_id,zkgl_name,zkgl_zks,shop_admin_id,zkgl_addtime,zkgl_remarks';
		$res = ZkglModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zkgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ZkglModel::destroy(['zkgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('zkgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zkgl_id,zkgl_name,zkgl_zks,zkgl_addtime,zkgl_remarks';
		$res = ZkglModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

