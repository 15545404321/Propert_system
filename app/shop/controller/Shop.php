<?php 
/*
 module:		物业信息控制器
 create_time:	2023-01-18 19:01:37
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Shop as ShopModel;
use think\facade\Db;

class Shop extends Admin {


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
			$where['shop_id'] = $this->request->post('shop_id', '', 'serach_in');
			$where['shop_name'] = ['like',$this->request->post('shop_name', '', 'serach_in')];
			$where['shop_skdw'] = $this->request->post('shop_skdw', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'shop_id desc';

			$sql ="select * from cd_shop where shop_id=".session("shop.shop_id")."";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,start_date,end_date,restrict_num,shop_skdw,shop_id,root,cname,account,password,create_time,update_time,disable';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Shop::class);

		$data['shop_address'] = implode('-',$data['shop_address']);
		$data['start_date'] = !empty($data['start_date']) ? strtotime($data['start_date']) : '';
		$data['end_date'] = !empty($data['end_date']) ? strtotime($data['end_date']) : '';
		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$data['create_time'] = time();

		Db::startTrans();

		try{
			$res = ShopModel::insertGetId($data);
			Db::connect('mysql')->name('shop_admin')->insert(array_merge($data,['shop_id'=>$res]));
			Db::commit();
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
			Db::rollback();
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'shop_id,shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,shop_skdw';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Shop::class);

		$data['shop_address'] = implode('-',$data['shop_address']);

		try{
			ShopModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('shop_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_id,shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,shop_skdw';
		$res = ShopModel::field($field)->find($id);
		$res['shop_address'] = explode('-',$res['shop_address']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('shop_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ShopModel::destroy(['shop_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('shop_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_id,shop_name,start_date,end_date,restrict_num,shop_skdw';
		$res = ShopModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

