<?php 
/*
 module:		欠费统计控制器
 create_time:	2023-03-16 14:52:52
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Qftj as QftjModel;
use think\facade\Db;

class Qftj extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['detail','delete','cxsc'])){
			$idx = $this->request->post('yssj_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = QftjModel::find($v);
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
			$where['yssj_id'] = $this->request->post('yssj_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');

			$where['a.yssj_fymc'] = $this->request->post('yssj_fymc', '', 'serach_in');

			$where['a.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');

			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');

			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

            $sql ="select 
sum(a.yssj_ysje)as yssj_sum,
b.fylx_name,a.fylx_id,
c.fybz_name,a.fybz_id,
concat_ws('-',z.louyu_lyqz,z.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,
e.member_name,e.member_id,e.member_tel,
concat_ws('-',x.tccd_name,w.cwqy_name,f.cewei_name) as cewei_name 
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_member as e on a.member_id = e.member_id 
left join cd_louyu as z on d.louyu_id = z.louyu_id
left join cd_cewei as f on a.cewei_id = f.cewei_id 
left join cd_tccd as x on x.tccd_id = f.tccd_id 
left join cd_cwqy as w on w.cwqy_id = f.cwqy_id
where a.yssj_stuats = 0
group by a.member_id,a.fybz_id,a.fylx_id
order by a.member_id asc";

			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;

			$data['data'] = $res;

			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id,lsys_id');

			return json($data);
		}
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id,lsys_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('cbgl_id',explode(',',$list))){
			$data['cbgl_ids'] = $this->query("select cbgl_id,cbgl_id from cd_cbgl where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		if(in_array('fybz_id',explode(',',$list))){
			$data['fybz_ids'] = $this->query("select fybz_id,fybz_name from cd_fybz where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('sjlx_id',explode(',',$list))){
			$data['sjlx_ids'] = $this->query("select sjlx_id,sjlx_name from cd_sjlx",'mysql');
		}
		if(in_array('zjys_id',explode(',',$list))){
			$data['zjys_ids'] = $this->query("select zjys_id,zjys_id from cd_zjys where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('lsys_id',explode(',',$list))){
			$data['lsys_ids'] = $this->query("select lsys_id as tval,lsys_id as tkey from cd_lsys where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getFcxx_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select
a.fcxx_id as tval,
concat_ws('-',c.louyu_lyqz,c.louyu_name,a.fcxx_fjbh) as tkey 
from cd_fcxx as a
left join cd_louyu as c on a.louyu_id = c.louyu_id 
where a.xqgl_id=".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getCewei_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select 
a.cewei_id as tval,

concat_ws('-',g.tccd_name,f.cwqy_name,a.cewei_name) as tkey
from cd_cewei as a 

left join cd_tccd as g on g.tccd_id = a.tccd_id 
left join cd_cwqy as f on f.cwqy_id = a.cwqy_id

where a.xqgl_id=".session('shop.xqgl_id')."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getMember_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getScys_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select scys_id as tval,scys_id as tkey from cd_scys where xqgl_id = ".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getCbpc_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select cbpc_id as tval,cbpc_id as tkey from cd_cbpc where xqgl_id = ".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
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
//		$data = $this->query('select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." where '.$sqlstr,'mysql');
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));

        return json(['status'=>200,'data'=>$data]);
    }
    /*end*/

}

