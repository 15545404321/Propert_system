<?php 
/*
 module:		广告管理控制器
 create_time:	2023-05-15 10:22:29
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\AdManage as AdManageModel;
use think\facade\Db;

class AdManage extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('admanage_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = AdManageModel::find($v);
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
			$where['admanage_id'] = $this->request->post('admanage_id', '', 'serach_in');
			$where['admanage_page'] = $this->request->post('admanage_page', '', 'serach_in');
			$where['admanage_position'] = $this->request->post('admanage_position', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$field = 'admanage_id,admanage_pic,admanage_page,admanage_position,shop_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'admanage_id desc';

			$query = AdManageModel::field($field);

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
		$postField = 'admanage_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['admanage_id']) throw new ValidateException ('参数错误');
		AdManageModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'admanage_pic,admanage_page,admanage_position,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\AdManage::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = AdManageModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'admanage_id,admanage_pic,admanage_page,admanage_position,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\AdManage::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			AdManageModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('admanage_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'admanage_id,admanage_pic,admanage_page,admanage_position,shop_id';
		$res = AdManageModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('admanage_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		AdManageModel::destroy(['admanage_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('admanage_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'admanage_id,admanage_pic,admanage_page,admanage_position,shop_id';
		$res = AdManageModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

