<?php 
/*
 module:		员工信息控制器
 create_time:	2023-01-03 21:29:39
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Ryxx as RyxxModel;
use think\facade\Db;

class Ryxx extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('ryxx_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = RyxxModel::find($v);
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
			$where['ryxx_id'] = $this->request->post('ryxx_id', '', 'serach_in');
			$where['ryxx.shop_admin_id'] = $this->request->post('shop_admin_id', '', 'serach_in');

			$where['ryxx.shop_id'] = session('shop.shop_id');
			$where['ryxx.ryxx_zaizhi'] = $this->request->post('ryxx_zaizhi', '', 'serach_in');
			$where['ryxx.ryxx_khh'] = $this->request->post('ryxx_khh', '', 'serach_in');
			$where['ryxx.ryxx_yhkh'] = $this->request->post('ryxx_yhkh', '', 'serach_in');

			$field = 'ryxx_id,ryxx_addtime,ryxx_xinzi,ryxx_gzjg,ryxx_baoxian,ryxx_zaizhi,ryxx_khh,ryxx_yhkh';

			$withJoin = [
				'shopadmin'=>explode(',','cname'),
				'xqgl'=>explode(',','xqgl_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ryxx_id desc';

			$query = RyxxModel::field($field);

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
		$postField = 'ryxx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['ryxx_id']) throw new ValidateException ('参数错误');
		RyxxModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,shop_admin_id,ryxx_addtime,ryxx_xinzi,ryxx_gzjg,ryxx_baoxian,ryxx_zaizhi,ryxx_khh,ryxx_yhkh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ryxx::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['ryxx_addtime'] = !empty($data['ryxx_addtime']) ? strtotime($data['ryxx_addtime']) : '';
		$data['ryxx_gzjg'] = getItemData($data['ryxx_gzjg']);
		$data['ryxx_baoxian'] = getItemData($data['ryxx_baoxian']);

		try{
			$res = RyxxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'ryxx_id,shop_id,xqgl_id,shop_admin_id,ryxx_addtime,ryxx_xinzi,ryxx_gzjg,ryxx_baoxian,ryxx_zaizhi,ryxx_khh,ryxx_yhkh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ryxx::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['ryxx_addtime'] = !empty($data['ryxx_addtime']) ? strtotime($data['ryxx_addtime']) : '';
		$data['ryxx_gzjg'] = getItemData($data['ryxx_gzjg']);
		$data['ryxx_baoxian'] = getItemData($data['ryxx_baoxian']);

		try{
			RyxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('ryxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ryxx_id,shop_id,xqgl_id,shop_admin_id,ryxx_addtime,ryxx_xinzi,ryxx_gzjg,ryxx_baoxian,ryxx_zaizhi,ryxx_khh,ryxx_yhkh';
		$res = RyxxModel::field($field)->find($id);
		$res['ryxx_gzjg'] = json_decode($res['ryxx_gzjg'],true);
		$res['ryxx_baoxian'] = json_decode($res['ryxx_baoxian'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('ryxx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		RyxxModel::destroy(['ryxx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('ryxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ryxx_id,ryxx_addtime,ryxx_xinzi,ryxx_gzjg,ryxx_baoxian,ryxx_zaizhi,ryxx_khh,ryxx_yhkh';
		$res = RyxxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

