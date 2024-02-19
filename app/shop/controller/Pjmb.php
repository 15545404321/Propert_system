<?php 
/*
 module:		票据模板控制器
 create_time:	2023-01-18 11:57:35
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Pjmb as PjmbModel;
use think\facade\Db;

class Pjmb extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('pjmb_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = PjmbModel::find($v);
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
			$where['pjmb_id'] = $this->request->post('pjmb_id', '', 'serach_in');
			$where['pjgl_id'] = $this->request->post('pjgl_id', '', 'serach_in');
			$where['pjmb_title'] = $this->request->post('pjmb_title', '', 'serach_in');
			$where['pjgl_gzdy'] = $this->request->post('pjgl_gzdy', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');

			$field = 'pjmb_id,pjgl_id,pjmb_title,pjmb_kuan,pjmb_gao,pjgl_gzdy,pjgl_gongzhang,pimb_gzwz';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'pjmb_id desc';

			$query = PjmbModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
			}

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'pjmb_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['pjmb_id']) throw new ValidateException ('参数错误');
		PjmbModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'pjgl_id,pjmb_title,pjmb_kuan,pjmb_gao,pjgl_gzdy,pjgl_gongzhang,pimb_gzwz,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Pjmb::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = PjmbModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'pjmb_id,pjgl_id,pjmb_title,pjmb_kuan,pjmb_gao,pjgl_gzdy,pjgl_gongzhang,pimb_gzwz,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Pjmb::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			PjmbModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('pjmb_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjmb_id,pjgl_id,pjmb_title,pjmb_kuan,pjmb_gao,pjgl_gzdy,pjgl_gongzhang,pimb_gzwz,shop_id,xqgl_id';
		$res = PjmbModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('pjmb_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		PjmbModel::destroy(['pjmb_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('pjmb_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjmb_id,pjgl_id,pjmb_title,pjmb_kuan,pjmb_gao,pjgl_gzdy,pjgl_gongzhang,pimb_gzwz';
		$res = PjmbModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

