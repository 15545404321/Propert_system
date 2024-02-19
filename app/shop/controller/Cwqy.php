<?php 
/*
 module:		车位区域控制器
 create_time:	2023-01-08 17:18:57
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Cwqy as CwqyModel;
use think\facade\Db;

class Cwqy extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('cwqy_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = CwqyModel::find($v);
					if($info['xqgl_id'] <> session('shop.xqgl_id')){
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
			$where['cwqy_id'] = $this->request->post('cwqy_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['cwqy_name'] = ['like',$this->request->post('cwqy_name', '', 'serach_in')];

			$field = 'cwqy_id,cwqy_name';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'cwqy_id desc';

			$query = CwqyModel::field($field);

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
		$postField = 'cwqy_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['cwqy_id']) throw new ValidateException ('参数错误');
		CwqyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,cwqy_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cwqy::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = CwqyModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'cwqy_id,shop_id,xqgl_id,cwqy_name';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cwqy::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			CwqyModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('cwqy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cwqy_id,shop_id,xqgl_id,cwqy_name';
		$res = CwqyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('cwqy_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		CwqyModel::destroy(['cwqy_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('cwqy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cwqy_id,cwqy_name';
		$res = CwqyModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

