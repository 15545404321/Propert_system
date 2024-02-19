<?php 
/*
 module:		费用分配控制器
 create_time:	2023-01-09 08:38:12
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Fplb as FplbModel;
use think\facade\Db;

class Fplb extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('fydy_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = FplbModel::find($v);
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
			$where['fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');
			$where['a.fydy_name'] = $this->request->post('fydy_name', '', 'serach_in');
			$where['a.fylb_id'] = $this->request->post('fylb_id', '', 'serach_in');
			$where['a.jflx_id'] = $this->request->post('jflx_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fydy_id desc';

			$sql ="select a.*,b.fylx_name,c.fylb_name,d.fydw_name from cd_fydy a join cd_fylx b on a.fylx_id=b.fylx_id join cd_fylb c on a.fylb_id=c.fylb_id join cd_fydw d on a.fydw_id=d.fydw_id where a.fylx_id in (1,2)";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fylx_id,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_cyskxm');
			return json($data);
		}
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fylx_id,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_cyskxm')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx where fylx_id=1 or fylx_id=2",'mysql');
		}
		if(in_array('fylb_id',explode(',',$list))){
			$data['fylb_ids'] = $this->query("select fylb_id,fylb_name from cd_fylb",'mysql');
		}
		if(in_array('fydw_id',explode(',',$list))){
			$data['fydw_ids'] = $this->query("select fydw_id,fydw_name from cd_fydw",'mysql');
		}
		if(in_array('jflx_id',explode(',',$list))){
			$data['jflx_ids'] = $this->query("select jflx_id,jflx_name from cd_jflx",'mysql');
		}
		if(in_array('qzfs_id',explode(',',$list))){
			$data['qzfs_ids'] = $this->query("select qzfs_id,qzfs_name from cd_qzfs",'mysql');
		}
		if(in_array('fydy_cyskxm',explode(',',$list))){
			$data['fydy_cyskxms'] = $this->query("select fydy_id,fydy_name from cd_fydy where fylx_id<>3",'mysql');
		}
		return $data;
	}



}

