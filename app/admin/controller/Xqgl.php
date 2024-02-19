<?php 
/*
 module:		项目管理控制器
 create_time:	2023-02-06 12:06:09
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\Xqgl as XqglModel;
use think\facade\Db;

class Xqgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('xqgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = XqglModel::find($v);
					if(session('admin.role_id') <> 1 && $info['shop_id'] <> session('admin.shop_id')){
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
			$where['xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');

			if(!in_array(session('admin.role_id'),[1])){
				$where['xqgl.shop_id'] = session('admin.shop_id');
			}
			$where['xqgl.xqgl_name'] = $this->request->post('xqgl_name', '', 'serach_in');

			$field = 'xqgl_id,xqgl_name,xqgl_glmj,xqgl_fjsl,xqgl_cwsl,xqgl_clsl,xqgl_yysf,xqgl_nysf,xqgl_address,xggl_jdwd';

			$withJoin = [
				'shop'=>explode(',','shop_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'xqgl_id desc';

			$query = XqglModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  新增项目
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_name,xqgl_glmj,xqgl_fjsl,xqgl_cwsl,xqgl_clsl,xqgl_yysf,xqgl_nysf,xqgl_address,xggl_jdwd';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Xqgl::class);

		$data['shop_id'] = session('admin.shop_id');
		$data['xqgl_address'] = implode('-',$data['xqgl_address']);

		if($ret = hook('hook/Xqgl@beforAdminAdd',$data)){
			return $ret;
		}

		try{
			$res = XqglModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'xqgl_id,shop_id,xqgl_name,xqgl_glmj,xqgl_fjsl,xqgl_cwsl,xqgl_clsl,xqgl_yysf,xqgl_nysf,xqgl_address,xggl_jdwd';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\Xqgl::class);

		$data['shop_id'] = session('admin.shop_id');
		$data['xqgl_address'] = implode('-',$data['xqgl_address']);

		if($ret = hook('hook/Xqgl@beforAdminUpdate',$data)){
			return $ret;
		}

		try{
			XqglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('xqgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xqgl_id,shop_id,xqgl_name,xqgl_glmj,xqgl_fjsl,xqgl_cwsl,xqgl_clsl,xqgl_yysf,xqgl_nysf,xqgl_address,xggl_jdwd';
		$res = XqglModel::field($field)->find($id);
		$res['xqgl_address'] = explode('-',$res['xqgl_address']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('xqgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Xqgl@beforAdminDelete',$idx)){
			return $ret;
		}

		XqglModel::destroy(['xqgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('xqgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'xqgl_id,xqgl_name,xqgl_glmj,xqgl_fjsl,xqgl_cwsl,xqgl_clsl,xqgl_yysf,xqgl_nysf,xqgl_address,xggl_jdwd';
		$res = XqglModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

