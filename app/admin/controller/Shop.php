<?php 
/*
 module:		物业管理控制器
 create_time:	2023-02-14 09:46:28
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Shop as ShopModel;
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
			$where['shop.shop_name'] = ['like',$this->request->post('shop_name', '', 'serach_in')];

			$field = 'shop_id,shop_name,start_date,end_date,goumai,restrict_num';

			$withJoin = [
				'shoprole'=>explode(',','name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'shop_id desc';

			$query = ShopModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['goumai']){
					$res['data'][$k]['goumai'] = Db::query("select name from  cd_shoprole where role_id=".$v['goumai']."")[0]['name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('goumai');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'shop_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['shop_id']) throw new ValidateException ('参数错误');
		ShopModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,start_date,end_date,goumai,restrict_num,shop_admin_id,shop_id,root,cname,account,password,create_time,update_time,disable,xqgl_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Shop::class);

		$data['shop_address'] = implode('-',$data['shop_address']);
		$data['start_date'] = !empty($data['start_date']) ? strtotime($data['start_date']) : '';
		$data['end_date'] = !empty($data['end_date']) ? strtotime($data['end_date']) : '';
		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$data['create_time'] = time();

		if($ret = hook('hook/Shop@beforAdminAdd',$data)){
			return $ret;
		}

		Db::startTrans();

		try{
			$res = ShopModel::insertGetId($data);
			Db::connect('mysql')->name('shop_admin')->insert(array_merge($data,['shop_id'=>$res]));
			Db::connect('mysql')->name('xqgl')->insert(array_merge($data,['shop_id'=>$res]));
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
		$postField = 'shop_id,shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,start_date,end_date,goumai,restrict_num';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Shop::class);

		$data['shop_address'] = implode('-',$data['shop_address']);
		$data['start_date'] = !empty($data['start_date']) ? strtotime($data['start_date']) : '';
		$data['end_date'] = !empty($data['end_date']) ? strtotime($data['end_date']) : '';

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
		$field = 'shop_id,shop_name,shop_address,shop_range,shop_xlr,shop_tel,shop_email,start_date,end_date,goumai,restrict_num';
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
		$withJoin = [
			'shoprole'=>explode(',','name'),
		];

		$field = 'shop_id,shop_name,start_date,end_date,goumai,restrict_num';
		$res = ShopModel::field($field)->withJoin($withJoin,'left')->find($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('goumai')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('goumai',explode(',',$list))){
			$data['goumais'] = $this->query("select role_id,name from cd_shoprole",'mysql');
		}
		return $data;
	}



}

