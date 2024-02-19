<?php 
/*
 module:		微信配置控制器
 create_time:	2023-02-09 11:31:00
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Wxpz as WxpzModel;
use think\facade\Db;

class Wxpz extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('wxpz_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = WxpzModel::find($v);
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
			$where['wxpz_id'] = $this->request->post('wxpz_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$field = 'wxpz_id,app_id,secret,mch_id,pay_sign_key,apiclient_cert,apiclient_key';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'wxpz_id desc';

			$query = WxpzModel::field($field);

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
		$postField = 'wxpz_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['wxpz_id']) throw new ValidateException ('参数错误');
		WxpzModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'app_id,secret,mch_id,pay_sign_key,apiclient_cert,apiclient_key,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Wxpz::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = WxpzModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'wxpz_id,app_id,secret,mch_id,pay_sign_key,apiclient_cert,apiclient_key,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Wxpz::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			WxpzModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('wxpz_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'wxpz_id,app_id,secret,mch_id,pay_sign_key,apiclient_cert,apiclient_key,shop_id';
		$res = WxpzModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('wxpz_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'wxpz_id,app_id,secret,mch_id,pay_sign_key,apiclient_cert,apiclient_key';
		$res = WxpzModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}

	/*start*/
    
    /*end*/

}

