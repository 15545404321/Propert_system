<?php 
/*
 module:		电话号码控制器
 create_time:	2023-02-10 10:03:20
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Bmdh as BmdhModel;
use think\facade\Db;

class Bmdh extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('bmdh_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = BmdhModel::find($v);
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
			$where['bmdh_id'] = $this->request->post('bmdh_id', '', 'serach_in');
			$where['bmdh_title'] = $this->request->post('bmdh_title', '', 'serach_in');
			$where['bmdh_neirong'] = $this->request->post('bmdh_neirong', '', 'serach_in');
			$where['bmdh_tel'] = $this->request->post('bmdh_tel', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['dhfl_id'] = $this->request->post('dhfl_id', '', 'serach_in');
			$where['bmdh_lxr'] = $this->request->post('bmdh_lxr', '', 'serach_in');

			$field = 'bmdh_id,bmdh_title,bmdh_neirong,bmdh_tel,bmdh_date,hmdh_end,dhfl_id,bmdh_lxr';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'bmdh_id desc';

			$query = BmdhModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['dhfl_id']){
					$res['data'][$k]['dhfl_id'] = Db::query("select dhfl_name from  cd_dhfl where xqgl_id = ".session("shop.xqgl_id")." and dhfl_id=".$v['dhfl_id']."")[0]['dhfl_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('dhfl_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'bmdh_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['bmdh_id']) throw new ValidateException ('参数错误');
		BmdhModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'bmdh_title,bmdh_neirong,bmdh_tel,bmdh_date,hmdh_end,shop_id,xqgl_id,dhfl_id,bmdh_lxr';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bmdh::class);

		$data['bmdh_date'] = !empty($data['bmdh_date']) ? strtotime($data['bmdh_date']) : '';
		$data['hmdh_end'] = !empty($data['hmdh_end']) ? strtotime($data['hmdh_end']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = BmdhModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'bmdh_id,bmdh_title,bmdh_neirong,bmdh_tel,bmdh_date,hmdh_end,shop_id,xqgl_id,dhfl_id,bmdh_lxr';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bmdh::class);

		$data['bmdh_date'] = !empty($data['bmdh_date']) ? strtotime($data['bmdh_date']) : '';
		$data['hmdh_end'] = !empty($data['hmdh_end']) ? strtotime($data['hmdh_end']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			BmdhModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('bmdh_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bmdh_id,bmdh_title,bmdh_neirong,bmdh_tel,bmdh_date,hmdh_end,shop_id,xqgl_id,dhfl_id,bmdh_lxr';
		$res = BmdhModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('bmdh_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		BmdhModel::destroy(['bmdh_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('bmdh_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bmdh_id,bmdh_title,bmdh_neirong,bmdh_tel,bmdh_date,hmdh_end,dhfl_id,bmdh_lxr';
		$res = BmdhModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('dhfl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('dhfl_id',explode(',',$list))){
			$data['dhfl_ids'] = $this->query("select dhfl_id,dhfl_name from cd_dhfl where xqgl_id = ".session("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}



}

