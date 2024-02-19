<?php 
namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Files as FileModel;
use app\shop\model\Adminuser as AdminuserModel;
use think\facade\Db;

class Base extends Admin {

	
	
	/*
 	* @Description 左侧菜单
 	*/
	public function getMenu(){
	    $menu =  $this->getBaseMenus();
		$mymenu = $this->getMyMenus(session('shop'),$menu);
//		dump($mymenu);exit;
		return json(['status'=>200,'data'=>$mymenu]);
	}
	
	
	//获取当前角色的菜单
	private function getMyMenus($roleInfo,$totalMenus){
		if(isset($roleInfo['role_id']) && $roleInfo['role_id'] == 1){
			return $totalMenus;
		}
		$tree = [];
		foreach($totalMenus as $key=>$val){
			if(in_array($val['access'],$roleInfo['access'])){
				$tree[] = array_merge($val,['children'=>$this->getMyMenus($roleInfo,$val['children'])]);
			}
		}
		return array_values($tree);
	}

	/*
 	* @Description 图片管理列表
 	*/
	function fileList(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];

		$field = 'id,filepath,hash,create_time';

		$res = FileModel::where(formatWhere($where))->field($field)->order('id desc')->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$data['status'] = 200;
		$data['data'] = $res;
		return json($data);
	}


	/*
 	* @Description  删除图片
 	*/
	function deleteFile(){
		$filepath =  $this->request->post('filepath', '', 'serach_in');
		if(!$filepath) $this->error('请选择图片');
		
		FileModel::where('filepath','in',$filepath)->delete();
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	
	/*
 	* @Description  重置密码
 	*/
	public function resetPwd(){
		$password = $this->request->post('password');
		
		if(empty($password)) $this->error('密码不能为空');
		
		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['password'] = md5($password.config('my.password_secrect'));
		
		db('shop_admin')->update($data);
		
		return json(['status'=>200,'msg'=>'操作成功']);
	}
	


}

