<?php 
/*
 module:		结算批次控制器
 create_time:	2023-01-13 13:21:57
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Xzpici as XzpiciModel;
use think\facade\Db;

class Xzpici extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete'])){
			$idx = $this->request->post('xzpici_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = XzpiciModel::find($v);
					if($info['shop_id'] <> session('shop.shop_id')){
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
			$where['xzpici_id'] = $this->request->post('xzpici_id', '', 'serach_in');

			$where['xzpici.shop_admin_id'] = session('shop.shop_admin_id');
			$where['xzpici.xz_ren'] = $this->request->post('xz_ren', '', 'serach_in');
			$where['xzpici.xz_jine'] = $this->request->post('xz_jine', '', 'serach_in');

			$where['xzpici.shop_id'] = session('shop.shop_id');
			$where['xzpici.xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');

			$field = 'xzpici_id,xz_ffdate,xz_zhouqi,addtime,xz_ren,xz_jine';

			$withJoin = [
				'shopadmin'=>explode(',','cname'),
				'xqgl'=>explode(',','xqgl_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'xzpici_id desc';

			$query = XzpiciModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$data['sum_xz_ren'] = $query->where(formatWhere($where))->sum('xz_ren');
			$data['sum_xz_jine'] = $query->where(formatWhere($where))->sum('xz_jine');
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('xqgl_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'xzpici_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['xzpici_id']) throw new ValidateException ('参数错误');
		XzpiciModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'xz_ffdate,xz_zhouqi,addtime,shop_admin_id,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xzpici::class);

		$data['xz_zhouqi'] = implode(',',$data['xz_zhouqi']);
		$data['addtime'] = time();
		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = XzpiciModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Xzpici@afterShopAdd',array_merge($data,['xzpici_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'xzpici_id,xz_ffdate,xz_zhouqi';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Xzpici::class);

		$data['xz_zhouqi'] = implode(',',$data['xz_zhouqi']);

		try{
			XzpiciModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Xzpici@afterShopUpdate',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('xzpici_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xzpici_id,xz_ffdate,xz_zhouqi';
		$res = XzpiciModel::field($field)->find($id);
		$res['xz_zhouqi'] = $res['xz_zhouqi'] ? explode(',',$res['xz_zhouqi']) : [];
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('xzpici_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Xzpici@beforShopDelete',$idx)){
			return $ret;
		}

		XzpiciModel::destroy(['xzpici_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('xqgl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('xqgl_id',explode(',',$list))){
			$data['xqgl_ids'] = $this->query("select xqgl_id,xqgl_name from cd_xqgl where shop_id=".session("shop.shop_id")."",'mysql');
		}
		return $data;
	}



}

