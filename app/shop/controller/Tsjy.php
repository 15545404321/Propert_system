<?php 
/*
 module:		投诉建议控制器
 create_time:	2023-03-23 11:30:03
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Tsjy as TsjyModel;
use think\facade\Db;

class Tsjy extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('tsjy_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = TsjyModel::find($v);
					if($info['shop_id'] <> session('shop.shop_id')){
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
			$where['tsjy_id'] = $this->request->post('tsjy_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');
			$where['member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['tsjy_tsnr'] = $this->request->post('tsjy_tsnr', '', 'serach_in');

			$field = 'tsjy_id,shop_id,member_id,tsjy_tsnr';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'tsjy_id desc';

			$query = TsjyModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
	* @Description  获取远程搜索字段信息
	*/
	public function remoteMemberidList(){
		$queryString = $this->request->post('queryString');
		$dataval = $this->request->post('dataval');
		if($queryString){
			$sqlstr = "member_name like '".$queryString."%'";
		}
		if($dataval){
			$sqlstr = 'member_id = '.$dataval;
		}
//		$data = $this->query('select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." where '.$sqlstr,'mysql');
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));

        return json(['status'=>200,'data'=>$data]);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'tsjy_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['tsjy_id']) throw new ValidateException ('参数错误');
		TsjyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,member_id,tsjy_tsnr';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Tsjy::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = TsjyModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'tsjy_id,shop_id,member_id,tsjy_tsnr';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Tsjy::class);

		$data['shop_id'] = session('shop.shop_id');

		try{
			TsjyModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('tsjy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'tsjy_id,shop_id,member_id,tsjy_tsnr';
		$res = TsjyModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('tsjy_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		TsjyModel::destroy(['tsjy_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('tsjy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'tsjy_id,shop_id,member_id,tsjy_tsnr';
		$res = TsjyModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}




}

