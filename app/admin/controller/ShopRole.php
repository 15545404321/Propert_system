<?php 
/*
 module:		功能角色控制器
 create_time:	2023-01-20 15:26:33
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\ShopRole as ShopRoleModel;
use think\facade\Db;

class ShopRole extends Admin {


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
			$where['role_id'] = $this->request->post('role_id', '', 'serach_in');
			$where['name'] = $this->request->post('name', '', 'serach_in');
			$where['status'] = $this->request->post('status', '', 'serach_in');
			$where['description'] = $this->request->post('description', '', 'serach_in');

			$field = 'role_id,name,status,description';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'role_id desc';

			$query = ShopRoleModel::field($field);

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
		$postField = 'role_id,status';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['role_id']) throw new ValidateException ('参数错误');
		ShopRoleModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'root,name,status,description,access';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopRole::class);

		$data['access'] = implode(',',$data['access']);

		try{
			$res = ShopRoleModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'role_id,root,name,status,description,access';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopRole::class);

		$data['access'] = implode(',',$data['access']);

		try{
			ShopRoleModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('role_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'role_id,root,name,status,description,access';
		$res = ShopRoleModel::field($field)->find($id);
		$res['access'] = explode(',',$res['access']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  权限节点
 	*/
	function getRoleAccess(){
		$nodes = (new \utils\AuthAccess())->getNodeMenus(298,0);

		if($baseNode = hook('hook/Base@getAdminNode',$nodes)){
			$nodes = array_merge($baseNode,$nodes);
		}

		array_multisort(array_column($nodes, 'sortid'),SORT_ASC,$nodes );
		return json(['status'=>200,'menus'=>$nodes]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('role_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ShopRoleModel::destroy(['role_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}




}

