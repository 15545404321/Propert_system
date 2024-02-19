<?php 
/*
 module:		钥匙分类控制器
 create_time:	2022-12-31 16:52:33
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Ysfl as YsflModel;
use think\facade\Db;

class Ysfl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('ysfl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = YsflModel::find($v);
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
			$where['ysfl_id'] = $this->request->post('ysfl_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['ysfl_name'] = $this->request->post('ysfl_name', '', 'serach_in');
			$where['ysfl_beizhu'] = $this->request->post('ysfl_beizhu', '', 'serach_in');

			$field = 'ysfl_id,ysfl_name,ysfl_xiangdan,ysfl_beizhu,ysfl_pid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ysfl_id desc';

			$query = YsflModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['ysfl_id','ysfl_pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('ysfl_pid');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'ysfl_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['ysfl_id']) throw new ValidateException ('参数错误');
		YsflModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,ysfl_name,ysfl_pid,ysfl_xiangdan,ysfl_beizhu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ysfl::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['ysfl_xiangdan'] = getItemData($data['ysfl_xiangdan']);

		try{
			$res = YsflModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'ysfl_id,shop_id,xqgl_id,ysfl_name,ysfl_pid,ysfl_xiangdan,ysfl_beizhu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ysfl::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		if(!isset($data['ysfl_pid'])){
			$data['ysfl_pid'] = null;
		}
		$data['ysfl_xiangdan'] = getItemData($data['ysfl_xiangdan']);

		try{
			YsflModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('ysfl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ysfl_id,shop_id,xqgl_id,ysfl_name,ysfl_pid,ysfl_xiangdan,ysfl_beizhu';
		$res = YsflModel::field($field)->find($id);
		$res['ysfl_xiangdan'] = json_decode($res['ysfl_xiangdan'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('ysfl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		YsflModel::destroy(['ysfl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('ysfl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ysfl_id,ysfl_name,ysfl_xiangdan,ysfl_beizhu';
		$res = YsflModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('ysfl_pid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('ysfl_pid',explode(',',$list))){
			$data['ysfl_pids'] = _generateSelectTree($this->query("select ysfl_id,ysfl_name,ysfl_pid from cd_ysfl",'mysql'));
		}
		return $data;
	}



}

