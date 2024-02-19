<?php 
/*
 module:		应收数据控制器
 create_time:	2023-01-14 14:21:53
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Yssj as YssjModel;
use think\facade\Db;

class Yssj extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['detail','delete'])){
			$idx = $this->request->post('yssj_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = YssjModel::find($v);
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
			$where['a.fcxx_id'] = ['like',$this->request->post('fcxx_id', '', 'serach_in')];
			$where['a.cewei_id'] = ['like',$this->request->post('cewei_id', '', 'serach_in')];
			$where['a.yssj_fymc'] = $this->request->post('yssj_fymc', '', 'serach_in');
			$where['a.yssj_cwyf'] = $this->request->post('yssj_cwyf', '', 'serach_in');
			$where['a.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');
			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['a.yssj_stuats'] = $this->request->post('yssj_stuats', '', 'serach_in');
			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['a.scys_id'] = $this->request->post('scys_id', '', 'serach_in');
            $where['a.lsys_id'] = $this->request->post('lsys_id', '', 'serach_in');
			$where['a.sjlx_id'] = $this->request->post('sjlx_id', '', 'serach_in');
			$where['a.zjys_id'] = $this->request->post('zjys_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

			$sql ="select 
a.*,
b.fylx_name,
c.fybz_name,
concat_ws('-',z.louyu_lyqz,z.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,
e.member_name,
concat_ws('-',x.tccd_name,w.cwqy_name,f.cewei_name) as cewei_name 

from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_member as e on a.member_id = e.member_id 
left join cd_louyu as z on d.louyu_id = z.louyu_id 
left join cd_cewei as f on a.cewei_id = f.cewei_id 
left join cd_tccd as x on x.tccd_id = f.tccd_id 
left join cd_cwqy as w on w.cwqy_id = f.cwqy_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'yssj_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['yssj_id']) throw new ValidateException ('参数错误');
		YssjModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*start*/
	/*
 	* @Description  明细列表
 	*/
	function indexLists(){
		if (!$this->request->isPost()){
			return view('indexLists');
		}else{
			$limit  = $this->request->post('limit', 20, 'intval');
			$page = $this->request->post('page', 1, 'intval');

			$where = [];
			$where['yssj_id'] = $this->request->post('yssj_id', '', 'serach_in');
			$where['syt_id'] = $this->request->post('syt_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

			$sql ="select a.*,b.fylx_name,c.fybz_name,concat_ws('-',z.louyu_lyqz,z.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,e.member_name,concat_ws('-',x.tccd_name,w.cwqy_name,f.cewei_name) as cewei_name 
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_member as e on a.member_id = e.member_id 
left join cd_louyu as z on d.louyu_id = z.louyu_id 
left join cd_cewei as f on a.cewei_id = f.cewei_id 
left join cd_tccd as x on x.tccd_id = f.tccd_id 
left join cd_cwqy as w on w.cwqy_id = f.cwqy_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id');
			return json($data);
		}
	}

	/*
 	* @Description  撤销收款
 	*/
	function yssjdelete(){
		$idx =  $this->request->post('yssj_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		/*if($ret = hook('hook/Yssj@beforShopDelete',$idx)){
			return $ret;
		}*/

		YssjModel::destroy(['yssj_id'=>explode(',',$idx)],true);

		/*if($ret = hook('hook/Yssj@afterShopDelete',$idx)){
			return $ret;
		}*/

		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  撤销收款
 	*/
	function delete(){
		$idx =  $this->request->post('yssj_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Yssj@beforShopDelete',$idx)){
			return $ret;
		}

		/*YssjModel::destroy(['yssj_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Yssj@afterShopDelete',$idx)){
			return $ret;
		}*/

		return json(['status'=>200,'msg'=>'操作成功']);
	}

    /*
     * @Description  重新生成
     */
    function cxsc(){
        $idx =  $this->request->post('yssj_id', '', 'serach_in');
//        dump($idx);
//        exit;
        if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Yssj@beforShopCxsc',$idx)){
            return $ret;
        }

//        YssjModel::destroy(['yssj_id'=>explode(',',$idx)],true);

        if($ret = hook('hook/Yssj@afterShopCxsc',$idx)){
            return $ret;
        }

        return json(['status'=>200,'msg'=>'操作成功']);
    }

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

    public function remoteFcxxidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        $scys_id = $this->request->post('scys_id');
        if($queryString){
            $sqlstr = "fcxx_fjbh like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'fcxx_id = '.$dataval;
        }
        
        $bbb = $this->query('select a.fcxx_id as tval, concat_ws("_",b.louyu_name,"商服/车库",a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is null and '.$sqlstr,'mysql');

        $scys_louyu_id = Db::name('scys')->where('scys_id',$scys_id)->value('louyu_id');
        $sqlstr .= 'and c.louyu_id = '.$scys_louyu_id;

        $aaa = $this->query('select a.fcxx_id as tval, concat_ws("_",c.louyu_name,b.louyu_name,a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id left join cd_louyu c on b.louyu_pid = c.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is not null and '.$sqlstr,'mysql');

        $data = _generateSelectTree(array_merge($aaa,$bbb));
        return json(['status'=>200,'data'=>$data]);
    }

    public function remoteCeweidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "cewei_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'cewei_id = '.$dataval;
        }
        $data = _generateSelectTree($this->query('select a.cewei_id as tval,concat_ws("_",g.tccd_name,f.cwqy_name,a.cewei_name) as tkey from cd_cewei as a left join cd_tccd as g on g.tccd_id = a.tccd_id left join cd_cwqy as f on f.cwqy_id = a.cwqy_id where a.xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));
//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id = '.session("shop.xqgl_id").' and '.$sqlstr,'mysql');
        return json(['status'=>200,'data'=>$data]);
    }

/*end*/

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id')]);
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



}

