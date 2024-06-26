<?php 
/*
 module:		文章管理控制器
 create_time:	2023-02-10 10:55:19
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Wzgl as WzglModel;
use think\facade\Db;

class Wzgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('wzgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = WzglModel::find($v);
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
			$where['wzgl_id'] = $this->request->post('wzgl_id', '', 'serach_in');
			$where['wzgl_title'] = $this->request->post('wzgl_title', '', 'serach_in');
			$where['wzgl_futitle'] = $this->request->post('wzgl_futitle', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['wzfl_id'] = $this->request->post('wzfl_id', '', 'serach_in');

			$field = 'wzgl_id,wzgl_title,wzgl_img,wzgl_futitle,wzgl_time,wzfl_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'wzgl_id desc';

			$query = WzglModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['wzfl_id']){
					$res['data'][$k]['wzfl_id'] = Db::query("select wzfl_name from  cd_wzfl where xqgl_id = ".session("shop.xqgl_id")." and wzfl_id=".$v['wzfl_id']."")[0]['wzfl_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('wzfl_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'wzgl_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['wzgl_id']) throw new ValidateException ('参数错误');
		WzglModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'wzgl_title,wzgl_img,wzgl_futitle,wzgl_neirong,wzgl_time,shop_id,xqgl_id,wzfl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Wzgl::class);

		$data['wzgl_time'] = !empty($data['wzgl_time']) ? strtotime($data['wzgl_time']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = WzglModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'wzgl_id,wzgl_title,wzgl_img,wzgl_futitle,wzgl_neirong,wzgl_time,shop_id,xqgl_id,wzfl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Wzgl::class);

		$data['wzgl_time'] = !empty($data['wzgl_time']) ? strtotime($data['wzgl_time']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			WzglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('wzgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'wzgl_id,wzgl_title,wzgl_img,wzgl_futitle,wzgl_neirong,wzgl_time,shop_id,xqgl_id,wzfl_id';
		$res = WzglModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('wzgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		WzglModel::destroy(['wzgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('wzgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'wzgl_id,wzgl_title,wzgl_img,wzgl_futitle,wzgl_time,wzfl_id';
		$res = WzglModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('wzfl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('wzfl_id',explode(',',$list))){
			$data['wzfl_ids'] = $this->query("select wzfl_id,wzfl_name from cd_wzfl where xqgl_id = ".session("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}



}

