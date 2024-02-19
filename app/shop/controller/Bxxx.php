<?php 
/*
 module:		报修信息控制器
 create_time:	2023-03-23 11:23:51
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Bxxx as BxxxModel;
use think\facade\Db;

class Bxxx extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail','fankui','getFankuiInfo','yezhufankui','getYezhufankuiInfo'])){
			$idx = $this->request->post('bxxx_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = BxxxModel::find($v);
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
			$where['bxxx_id'] = $this->request->post('bxxx_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['bxxx_miaoshu'] = $this->request->post('bxxx_miaoshu', '', 'serach_in');
			$where['member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['cname'] = $this->request->post('cname', '', 'serach_in');
			$where['bxxx_fankui'] = $this->request->post('bxxx_fankui', '', 'serach_in');
			$where['bxxx_pingjia'] = $this->request->post('bxxx_pingjia', '', 'serach_in');
			$where['bxxx_start'] = $this->request->post('bxxx_start', '', 'serach_in');
			$where['bxfl_id'] = $this->request->post('bxfl_id', '', 'serach_in');

			$field = 'bxxx_id,bxxx_miaoshu,bxxx_pic,bxxx_time,member_id,cname,bxxx_fankui,bxxx_cltime,bxxx_pingfen,bxxx_pingjia,bxxx_start,bxfl_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'bxxx_id desc';

			$query = BxxxModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['member_id']){
					$res['data'][$k]['member_id'] = Db::query("select member_name from  cd_member where xqgl_id = ".session("shop.xqgl_id")." and member_id=".$v['member_id']."")[0]['member_name'];
				}
				if($v['bxfl_id']){
					$res['data'][$k]['bxfl_id'] = Db::query("select bxfl_name from  cd_bxfl where xqgl_id = ".session("shop.xqgl_id")." and bxfl_id=".$v['bxfl_id']."")[0]['bxfl_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cname,bxfl_id');
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
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));

//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id = '.session("shop.xqgl_id").' and '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'bxxx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['bxxx_id']) throw new ValidateException ('参数错误');
		BxxxModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加报修
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,bxxx_miaoshu,bxxx_pic,bxxx_time,member_id,bxfl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bxxx::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['bxxx_pic'] = getItemData($data['bxxx_pic']);
		$data['bxxx_time'] = time();

		try{
			$res = BxxxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'bxxx_id,shop_id,xqgl_id,bxxx_miaoshu,bxxx_pic,bxxx_time,member_id,cname,bxxx_fankui,bxxx_cltime,bxxx_pingfen,bxxx_pingjia,bxxx_start,bxfl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bxxx::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['bxxx_pic'] = getItemData($data['bxxx_pic']);
		$data['bxxx_time'] = !empty($data['bxxx_time']) ? strtotime($data['bxxx_time']) : '';
		$data['bxxx_cltime'] = !empty($data['bxxx_cltime']) ? strtotime($data['bxxx_cltime']) : '';

		try{
			BxxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('bxxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bxxx_id,shop_id,xqgl_id,bxxx_miaoshu,bxxx_pic,bxxx_time,member_id,cname,bxxx_fankui,bxxx_cltime,bxxx_pingfen,bxxx_pingjia,bxxx_start,bxfl_id';
		$res = BxxxModel::field($field)->find($id);
		$res['bxxx_pic'] = json_decode($res['bxxx_pic'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('bxxx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		BxxxModel::destroy(['bxxx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('bxxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bxxx_id,bxxx_miaoshu,bxxx_pic,bxxx_time,member_id,cname,bxxx_fankui,bxxx_cltime,bxxx_pingfen,bxxx_pingjia,bxxx_start,bxfl_id';
		$res = BxxxModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  工程反馈
 	*/
	public function fankui(){
		$postField = 'bxxx_id,cname,bxxx_fankui,bxxx_cltime';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bxxx::class);

		$data['bxxx_cltime'] = !empty($data['bxxx_cltime']) ? strtotime($data['bxxx_cltime']) : '';

		try{
			BxxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getFankuiInfo(){
		$id =  $this->request->post('bxxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bxxx_id,cname,bxxx_fankui,bxxx_cltime';
		$res = BxxxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  业主反馈
 	*/
	public function yezhufankui(){
		$postField = 'bxxx_id,bxxx_pingfen,bxxx_pingjia,bxxx_start';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Bxxx::class);

		try{
			BxxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getYezhufankuiInfo(){
		$id =  $this->request->post('bxxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'bxxx_id,bxxx_pingfen,bxxx_pingjia,bxxx_start';
		$res = BxxxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('cname,bxfl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('cname',explode(',',$list))){
			$data['cnames'] = $this->query("select cname,cname from cd_shop_admin where shop_id = ".session("shop.shop_id")."",'mysql');
		}
		if(in_array('bxfl_id',explode(',',$list))){
			$data['bxfl_ids'] = $this->query("select bxfl_id,bxfl_name from cd_bxfl where xqgl_id = ".session("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}



}

