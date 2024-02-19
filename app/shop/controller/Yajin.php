<?php 
/*
 module:		押金台账控制器
 create_time:	2023-01-16 15:19:19
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Yajin as YajinModel;
use think\facade\Db;

class Yajin extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail','yjdetail'])){
			$idx = $this->request->post('zjys_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = YajinModel::find($v);
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
			$where['zjys_id'] = $this->request->post('zjys_id', '', 'serach_in');
			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['a.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.fylx_id'] = $this->request->post('fylx_id', '5', 'serach_in');
			$where['a.tui_status'] = $this->request->post('tui_status', '', 'serach_in');

			$tui_time = $this->request->post('tui_time', '', 'serach_in');
			$where['a.tui_time'] = ['between',[strtotime($tui_time[0]),strtotime($tui_time[1])]];
			$where['a.tui_beizhu'] = $this->request->post('tui_beizhu', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zjys_id desc';

			$sql ="select 
a.*,
concat_ws('-',z.louyu_lyqz,z.louyu_name,b.fcxx_fjbh) as fcxx_fjbh,
c.fydy_name,
d.fybz_name,
e.member_name,
f.fylx_name

from cd_zjys as a 
left join cd_fcxx b on a.fcxx_id = b.fcxx_id 
left join cd_fydy c on a.fydy_id = c.fydy_id 
left join cd_fybz d on a.fybz_id = d.fybz_id 
left join cd_member e on a.member_id = e.member_id 
left join cd_fylx f on a.fylx_id = f.fylx_id 
left join cd_louyu z on z.louyu_id = b.louyu_id ";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fylx_id');
			return json($data);
		}
	}


	/*
	* @Description  获取远程搜索字段信息
	*/
	public function remoteFcxxidList(){
		$queryString = $this->request->post('queryString');
		$dataval = $this->request->post('dataval');
		if($queryString){
			$sqlstr = "fcxx_fjbh like '".$queryString."%'";
		}
		if($dataval){
			$sqlstr = 'fcxx_id = '.$dataval;
		}
		$data = $this->query('select 
fcxx_id as tval,
concat_ws("_",b.louyu_lyqz,b.louyu_name,a.fcxx_fjbh) as tkey 
from cd_fcxx a 
left join cd_louyu b on a.louyu_id=b.louyu_id 
where a.xqgl_id='.session('shop.xqgl_id').' and a.shop_id='.session('shop.shop_id').' where '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'zjys_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['zjys_id']) throw new ValidateException ('参数错误');
		YajinModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  修改状态
 	*/
	public function update(){
		$postField = 'zjys_id,tui_status,tui_time,tui_beizhu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Yajin::class);

		$data['tui_time'] = !empty($data['tui_time']) ? strtotime($data['tui_time']) : '';

		try{
			YajinModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Yajin@afterShopUpdate',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zjys_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zjys_id,tui_status,tui_time,tui_beizhu';
		$res = YajinModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('zjys_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zjys_id,zjys_dcys,zjys_sysl,zjys_bcys,zjys_ktime,zjys_jtime,tui_status,tui_time,tui_beizhu';
		$res = YajinModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  押金详情
 	*/
	function yjdetail(){
		$id =  $this->request->post('zjys_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zjys_id,zjys_dcys,zjys_sysl,zjys_bcys,zjys_ktime,zjys_jtime,tui_status,tui_time,tui_beizhu';
		$res = YajinModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fylx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		return $data;
	}



}

