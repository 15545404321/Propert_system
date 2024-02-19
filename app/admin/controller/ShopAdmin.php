<?php 
/*
 module:		物业账号控制器
 create_time:	2023-01-20 12:15:17
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\ShopAdmin as ShopAdminModel;
use think\facade\Db;

class ShopAdmin extends Admin {


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
			$where['shop_admin_id'] = $this->request->post('shop_admin_id', '', 'serach_in');
			$where['shop_id'] = $this->request->post('shop_id', '', 'serach_in');
			$where['root'] = $this->request->post('root', '', 'serach_in');
			$where['cname'] = $this->request->post('cname', '', 'serach_in');
			$where['account'] = $this->request->post('account', '', 'serach_in');
			$where['disable'] = $this->request->post('disable', '', 'serach_in');
			$where['root'] = '1';

			$field = 'shop_admin_id,cname,account,create_time,disable';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'shop_admin_id desc';

			$query = ShopAdminModel::field($field);

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
		$postField = 'shop_admin_id,disable';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['shop_admin_id']) throw new ValidateException ('参数错误');
		ShopAdminModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,root,cname,account,password,create_time,update_time,disable';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopAdmin::class);

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$data['create_time'] = time();

		try{
			$res = ShopAdminModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'shop_admin_id,shop_id,root,cname,account,create_time,update_time,disable';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopAdmin::class);

		$data['create_time'] = !empty($data['create_time']) ? strtotime($data['create_time']) : '';
		$data['update_time'] = time();

		try{
			ShopAdminModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('shop_admin_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_admin_id,shop_id,root,cname,account,create_time,update_time,disable';
		$res = ShopAdminModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('shop_admin_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ShopAdminModel::destroy(['shop_admin_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  重置密码
 	*/
	public function resetPwd(){
		$postField = 'shop_admin_id,password';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['shop_admin_id'])) throw new ValidateException ('参数错误');
		if(empty($data['password'])) throw new ValidateException ('密码不能为空');

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$res = ShopAdminModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}




}

