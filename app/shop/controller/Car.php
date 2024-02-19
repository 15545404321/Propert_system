<?php 
/*
 module:		车辆管理控制器
 create_time:	2023-03-22 13:52:46
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Car as CarModel;
use think\facade\Db;

class Car extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','zcgl','getZcglInfo'])){
			$idx = $this->request->post('car_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = CarModel::find($v);
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
			$where['car_id'] = $this->request->post('car_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['car_name'] = $this->request->post('car_name', '', 'serach_in');
			$where['member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['car_type'] = $this->request->post('car_type', '', 'serach_in');
			$where['car_ppxh'] = $this->request->post('car_ppxh', '', 'serach_in');

			$field = 'car_id,car_name,member_id,car_type,car_ppxh,car_addtime,car_endtime';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'car_id desc';

			$query = CarModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['member_id']){
					$res['data'][$k]['member_id'] = Db::query("select member_name from  cd_member where xqgl_id=".session("shop.xqgl_id")." and member_id=".$v['member_id']."")[0]['member_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'car_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['car_id']) throw new ValidateException ('参数错误');
		CarModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,car_name,member_id,car_type,car_ppxh,car_addtime,car_endtime';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Car::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['car_addtime'] = time();
		$data['car_endtime'] = !empty($data['car_endtime']) ? strtotime($data['car_endtime']) : '';

		try{
			$res = CarModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'car_id,shop_id,xqgl_id,car_name,member_id,car_type,car_ppxh,car_endtime';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Car::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['car_endtime'] = !empty($data['car_endtime']) ? strtotime($data['car_endtime']) : '';

		try{
			CarModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('car_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'car_id,shop_id,xqgl_id,car_name,member_id,car_type,car_ppxh,car_endtime';
		$res = CarModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('car_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		CarModel::destroy(['car_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['car_id'] = ['in',$this->request->param('car_id', '', 'serach_in')];

		$where['car.shop_id'] = session('shop.shop_id');

		$where['car.xqgl_id'] = session('shop.xqgl_id');
		$where['car.car_name'] = $this->request->param('car_name', '', 'serach_in');
		$where['car.member_id'] = $this->request->param('member_id', '', 'serach_in');
		$where['car.car_type'] = $this->request->param('car_type', '', 'serach_in');
		$where['car.car_ppxh'] = $this->request->param('car_ppxh', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'car_id desc';

		$field = 'car_name,car_type,car_ppxh,car_endtime,car_addtime';

		$withJoin = [
			'shop'=>explode(',','shop_name'),
			'xqgl'=>explode(',','xqgl_name'),
			'member'=>explode(',','member_name'),
		];

		$res = CarModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		foreach($res['data'] as $key=>$val){
			$res['data'][$key]['car_type'] = getItemVal($val['car_type'],'[{"key":"固定车辆","val":"1","label_color":"success"},{"key":"临时车辆","val":"2","label_color":"info"}]');
			$res['data'][$key]['car_addtime'] = !empty($val['car_addtime']) ? date('Y-m-d H:i:s',$val['car_addtime']) : '';
			$res['data'][$key]['car_endtime'] = !empty($val['car_endtime']) ? date('Y-m-d',$val['car_endtime']) : '';
			$res['data'][$key]['shop_name'] = $val['shop']['shop_name'];
			unset($res['data'][$key]['shop']);
			$res['data'][$key]['xqgl_name'] = $val['xqgl']['xqgl_name'];
			unset($res['data'][$key]['xqgl']);
			$res['data'][$key]['member_name'] = $val['member']['member_name'];
			unset($res['data'][$key]['member']);
			unset($res['data'][$key]['car_id']);
		}

		$data['status'] = 200;
		$data['header'] = explode(',','车牌号,车辆性质,品牌型号,录入时间,到期时间,公司名称,项目名称,客户名称');
		$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
		$data['filename'] = '车辆管理.'.config('my.dump_extension');
		$data['data'] = $res['data'];
		return json($data);
	}


	/*
 	* @Description  资产关联
 	*/
	public function zcgl(){
		$postField = 'car_id,member_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Car::class);

		try{
			CarModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getZcglInfo(){
		$id =  $this->request->post('car_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'car_id,member_id';
		$res = CarModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}



/*start*/
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

//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}
/*end*/



}

