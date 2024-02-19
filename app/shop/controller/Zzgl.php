<?php 
/*
 module:		部门管理控制器
 create_time:	2023-01-09 14:44:32
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zzgl as ZzglModel;
use think\facade\Db;

class Zzgl extends Admin {


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
			$where['zzgl_id'] = $this->request->post('zzgl_id', '', 'serach_in');
			$where['zzgl_bmmc'] = ['like',$this->request->post('zzgl_bmmc', '', 'serach_in')];
			$where['xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zzgl_px asc';

			$sql ="select * from cd_zzgl where shop_id=".session("shop.shop_id")."";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			foreach($res['data'] as $k=>$v){
				if($v['xqgl_id']){
					$res['data'][$k]['xqgl_id'] = Db::query("select xqgl_name from  cd_xqgl where shop_id=".session("shop.shop_id")." and xqgl_id=".$v['xqgl_id']."")[0]['xqgl_name'];
				}
			}

			$res['data'] = _generateListTree($res['data'],0,['zzgl_id','zzgl_sjbm']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('xqgl_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getZzgl_sjbm(){
		$xqgl_id =  $this->request->post('xqgl_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = _generateSelectTree($this->query('select zzgl_id,zzgl_bmmc,zzgl_sjbm from cd_zzgl where shop_id = '.session('shop.shop_id').' and xqgl_id ='.$xqgl_id,'mysql'));
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'zzgl_id,zzgl_px';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['zzgl_id']) throw new ValidateException ('参数错误');
		ZzglModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,zzgl_bmmc,xqgl_id,zzgl_sjbm,zzgl_px';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zzgl::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = ZzglModel::insertGetId($data);
			if($res && empty($data['zzgl_px'])){
				ZzglModel::update(['zzgl_px'=>$res,'zzgl_id'=>$res]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'zzgl_id,shop_id,zzgl_bmmc,xqgl_id,zzgl_sjbm,zzgl_px';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zzgl::class);

		$data['shop_id'] = session('shop.shop_id');

		if(!isset($data['zzgl_sjbm'])){
			$data['zzgl_sjbm'] = null;
		}

		try{
			ZzglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zzgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zzgl_id,shop_id,zzgl_bmmc,xqgl_id,zzgl_sjbm,zzgl_px';
		$res = ZzglModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zzgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ZzglModel::destroy(['zzgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  所属项目
 	*/
	public function batupdate(){
		$postField = 'zzgl_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zzgl::class);

		$idx = explode(',',$data['zzgl_id']);
		unset($data['zzgl_id']);

		try{
			ZzglModel::where(['zzgl_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
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

