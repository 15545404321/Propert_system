<?php 
/*
 module:		费用标准控制器
 create_time:	2023-01-17 11:14:32
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Fybz as FybzModel;
use think\facade\Db;

class Fybz extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('fybz_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = FybzModel::find($v);
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
			$where['fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['fybz.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['fybz.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');

			$where['fybz.shop_id'] = session('shop.shop_id');

			$where['fybz.xqgl_id'] = session('shop.xqgl_id');
			$where['fybz.fybz_name'] = $this->request->post('fybz_name', '', 'serach_in');

			$field = 'fybz_id,fybz_name,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';

			$withJoin = [
				'jfgs'=>explode(',','jfgs_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fybz_id desc';

			$query = FybzModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('jfgs_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'fybz_id,fybz_status';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['fybz_id']) throw new ValidateException ('参数错误');
		FybzModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,fydy_id,fylx_id,xqgl_id,fybz_name,jfgs_id,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fybz::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = FybzModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'fybz_id,shop_id,fydy_id,fylx_id,xqgl_id,fybz_name,jfgs_id,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fybz::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			FybzModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('fybz_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fybz_id,shop_id,fydy_id,fylx_id,xqgl_id,fybz_name,jfgs_id,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';
		$res = FybzModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('fybz_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		FybzModel::destroy(['fybz_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('fybz_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fybz_id,fybz_name,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';
		$res = FybzModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('jfgs_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('jfgs_id',explode(',',$list))){
			$data['jfgs_ids'] = $this->query("select jfgs_id,jfgs_name from cd_jfgs",'mysql');
		}
		return $data;
	}



}

