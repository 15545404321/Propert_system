<?php 
/*
 module:		关联资产管理控制器
 create_time:	2022-12-26 08:38:38
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Glzcgl as GlzcglModel;
use think\facade\Db;

class Glzcgl extends Admin {


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
			$where['glzcgl_id'] = $this->request->post('glzcgl_id', '', 'serach_in');
			$where['glzcgl.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['glzcgl.zclx_id'] = $this->request->post('zclx_id', '', 'serach_in');
			$where['glzcgl.louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['glzcgl.tccd_id'] = $this->request->post('tccd_id', '', 'serach_in');
			$where['glzcgl.cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
			$where['glzcgl.khlx_id'] = $this->request->post('khlx_id', '', 'serach_in');
			$where['glzcgl.glzcgl_type'] = ['find in set',$this->request->post('glzcgl_type', '', 'serach_in')];

			$field = 'glzcgl_id,start_time,end_time,glzcgl_type,glzcgl_time';

			$withJoin = [
				'zclx'=>explode(',','zclx_name'),
				'shopadmin'=>explode(',','cname'),
				'khlx'=>explode(',','khlx_name'),
				'louyu'=>explode(',','louyu_name'),
				'fcxx'=>explode(',','fcxx_fjbh'),
				'tccd'=>explode(',','tccd_name'),
				'cewei'=>explode(',','cewei_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'glzcgl_id desc';

			$query = GlzcglModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('zclx_id,louyu_id,tccd_id,khlx_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_id(){
		$louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where shop_id='.session('shop.shop_id').' and xqgl_id='.session('shop.xqgl_id').' and louyu_id ='.$louyu_id,'mysql');
		return json($data);
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getCewei_id(){
		$tccd_id =  $this->request->post('tccd_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select cewei_id,cewei_name from cd_cewei where shop_id='.session('shop.shop_id').' and xqgl_id='.session('shop.xqgl_id').' and tccd_id ='.$tccd_id,'mysql');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'glzcgl_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['glzcgl_id']) throw new ValidateException ('参数错误');
		GlzcglModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,member_id,zclx_id,louyu_id,fcxx_id,tccd_id,cewei_id,start_time,end_time,khlx_id,glzcgl_type,shop_admin_id,glzcgl_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Glzcgl::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['start_time'] = !empty($data['start_time']) ? strtotime($data['start_time']) : '';
		$data['end_time'] = !empty($data['end_time']) ? strtotime($data['end_time']) : '';
		$data['glzcgl_type'] = implode(',',$data['glzcgl_type']);
		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['glzcgl_time'] = time();

		try{
			$res = GlzcglModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'glzcgl_id,shop_id,xqgl_id,member_id,zclx_id,louyu_id,fcxx_id,tccd_id,cewei_id,start_time,end_time,khlx_id,glzcgl_type,shop_admin_id,glzcgl_time';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Glzcgl::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		if(!isset($data['louyu_id'])){
			$data['louyu_id'] = null;
		}
		$data['start_time'] = !empty($data['start_time']) ? strtotime($data['start_time']) : '';
		$data['end_time'] = !empty($data['end_time']) ? strtotime($data['end_time']) : '';
		$data['glzcgl_type'] = implode(',',$data['glzcgl_type']);
		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['glzcgl_time'] = !empty($data['glzcgl_time']) ? strtotime($data['glzcgl_time']) : '';

		try{
			GlzcglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('glzcgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'glzcgl_id,shop_id,xqgl_id,member_id,zclx_id,louyu_id,fcxx_id,tccd_id,cewei_id,start_time,end_time,khlx_id,glzcgl_type,shop_admin_id,glzcgl_time';
		$res = GlzcglModel::field($field)->find($id);
		$res['glzcgl_type'] = explode(',',$res['glzcgl_type']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('glzcgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		GlzcglModel::destroy(['glzcgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('glzcgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'glzcgl_id,start_time,end_time,glzcgl_type,glzcgl_time';
		$res = GlzcglModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('zclx_id,louyu_id,tccd_id,khlx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('zclx_id',explode(',',$list))){
			$data['zclx_ids'] = $this->query("select zclx_id,zclx_name from cd_zclx",'mysql');
		}
		if(in_array('louyu_id',explode(',',$list))){
			$data['louyu_ids'] = _generateSelectTree($this->query("select louyu_id,louyu_name,louyu_pid from cd_louyu where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",'mysql'));
		}
		if(in_array('tccd_id',explode(',',$list))){
			$data['tccd_ids'] = $this->query("select tccd_id,tccd_name from cd_tccd where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('khlx_id',explode(',',$list))){
			$data['khlx_ids'] = $this->query("select khlx_id,khlx_name from cd_khlx",'mysql');
		}
		return $data;
	}



}

