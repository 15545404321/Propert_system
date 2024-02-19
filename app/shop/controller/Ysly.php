<?php 
/*
 module:		领用记录控制器
 create_time:	2023-01-06 16:02:08
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Ysly as YslyModel;
use think\facade\Db;

class Ysly extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('ysly_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = YslyModel::find($v);
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
			$where['ysly_id'] = $this->request->post('ysly_id', '', 'serach_in');

			$where['ysly.shop_id'] = session('shop.shop_id');

			$where['ysly.xqgl_id'] = session('shop.xqgl_id');
			$where['ysly.ysfl_id'] = $this->request->post('ysfl_id', '', 'serach_in');
			$where['ysly.ys_user'] = $this->request->post('ys_user', '', 'serach_in');
			$where['ysly.ys_state'] = $this->request->post('ys_state', '', 'serach_in');
			$where['ysly.ys_beizhu'] = $this->request->post('ys_beizhu', '', 'serach_in');

			$field = 'ysly_id,ys_lingyong,ys_user,ys_state,ys_ghtime';

			$withJoin = [
				'ysfl'=>explode(',','ysfl_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ysly_id desc';

			$query = YslyModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('ysfl_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'ysly_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['ysly_id']) throw new ValidateException ('参数错误');
		YslyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,ysfl_id,ys_lingyong,ys_user,ys_beizhu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ysly::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['ys_lingyong'] = !empty($data['ys_lingyong']) ? strtotime($data['ys_lingyong']) : '';

		try{
			$res = YslyModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'ysly_id,shop_id,xqgl_id,ysfl_id,ys_lingyong,ys_user,ys_state,ys_ghtime,ys_beizhu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ysly::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		if(!isset($data['ysfl_id'])){
			$data['ysfl_id'] = null;
		}
		$data['ys_lingyong'] = !empty($data['ys_lingyong']) ? strtotime($data['ys_lingyong']) : '';
		$data['ys_ghtime'] = !empty($data['ys_ghtime']) ? strtotime($data['ys_ghtime']) : '';

		try{
			YslyModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('ysly_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ysly_id,shop_id,xqgl_id,ysfl_id,ys_lingyong,ys_user,ys_state,ys_ghtime,ys_beizhu';
		$res = YslyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('ysly_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		YslyModel::destroy(['ysly_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('ysly_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ysly_id,ys_lingyong,ys_user,ys_state,ys_ghtime';
		$res = YslyModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['ysly_id'] = ['in',$this->request->param('ysly_id', '', 'serach_in')];

		$where['ysly.shop_id'] = session('shop.shop_id');

		$where['ysly.xqgl_id'] = session('shop.xqgl_id');
		$where['ysly.ysfl_id'] = $this->request->param('ysfl_id', '', 'serach_in');
		$where['ysly.ys_user'] = $this->request->param('ys_user', '', 'serach_in');
		$where['ysly.ys_state'] = $this->request->param('ys_state', '', 'serach_in');
		$where['ysly.ys_beizhu'] = $this->request->param('ys_beizhu', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'ysly_id desc';

		$field = 'ysfl_id,ys_lingyong,ys_user,ys_state,ys_beizhu,ys_ghtime';

		$withJoin = [
			'ysfl'=>explode(',','ysfl_name'),
		];

		$res = YslyModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		foreach($res['data'] as $key=>$val){
			$res['data'][$key]['ys_lingyong'] = !empty($val['ys_lingyong']) ? date('Y-m-d H:i:s',$val['ys_lingyong']) : '';
			$res['data'][$key]['ys_state'] = getItemVal($val['ys_state'],'[{"key":"使用","val":"1","label_color":"success"},{"key":"归还","val":"2","label_color":"danger"}]');
			$res['data'][$key]['ys_ghtime'] = !empty($val['ys_ghtime']) ? date('Y-m-d H:i:s',$val['ys_ghtime']) : '';
			$res['data'][$key]['ysfl_name'] = $val['ysfl']['ysfl_name'];
			unset($res['data'][$key]['ysfl']);
			unset($res['data'][$key]['ysly_id']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','钥匙名称,领用时间,领用人员,使用状态,归还时间,领用备注,名称/分类');
		$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
		$data['filename'] = '领用记录.'.config('my.dump_extension');
		$data['data'] = $res['data'];
		return json($data);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('ysfl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('ysfl_id',explode(',',$list))){
			$data['ysfl_ids'] = _generateSelectTree($this->query("select ysfl_id,ysfl_name,ysfl_pid from cd_ysfl where xqgl_id=".session("shop.xqgl_id")."",'mysql'));
		}
		return $data;
	}



}

