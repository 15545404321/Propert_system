<?php 
/*
 module:		过户记录控制器
 create_time:	2023-01-26 11:43:38
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Ghjl as GhjlModel;
use think\facade\Db;

class Ghjl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['tuihui','getTuihuiInfo'])){
			$idx = $this->request->post('ghjl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = GhjlModel::find($v);
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
			$where['ghjl_id'] = $this->request->post('ghjl_id', '', 'serach_in');
			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['a.member_idb'] = $this->request->post('member_idb', '', 'serach_in');
			$where['a.member_nameb'] = $this->request->post('member_nameb', '', 'serach_in');
			$where['a.ghjl_jiesuan'] = $this->request->post('ghjl_jiesuan', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.fcxx_id'] = ['like',$this->request->post('fcxx_id', '', 'serach_in')];
			$where['a.cewei_id'] = ['like',$this->request->post('cewei_id', '', 'serach_in')];
			$where['a.gh_tui'] = $this->request->post('gh_tui', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'ghjl_id desc';

			$sql ="select 
a.*,
b.member_name as member_namea,
c.member_name as member_nameb,
concat_ws('-',e.louyu_lyqz,e.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,
concat_ws('-',g.tccd_name,h.cwqy_name,f.cewei_name) as cewei_name 
from cd_ghjl as a 
left join cd_member as b on a.member_id = b.member_id 
left join cd_member as c on a.member_idb = c.member_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_louyu as e on d.louyu_id = e.louyu_id 
left join cd_cewei as f on f.cewei_id = a.cewei_id 
left join cd_tccd as g on g.tccd_id = f.tccd_id 
left join cd_cwqy as h on h.cwqy_id = f.cwqy_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cewei_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_id(){
		$member_id =  $this->request->post('member_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = _generateSelectTree($this->query('select fcxx_id as tval,concat_ws("-",b.louyu_lyqz,b.louyu_name,a.fcxx_fjbh) as tkey from cd_fcxx a
left join cd_louyu b on a.louyu_id=b.louyu_id where member_id ='.$member_id,'mysql'));
		return json($data);
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
	* @Description  获取远程搜索字段信息
	*/
	public function remoteMemberidbList(){
		$queryString = $this->request->post('queryString');
		$dataval = $this->request->post('dataval');
		if($queryString){
			$sqlstr = "member_name like '".$queryString."%'";
		}
		if($dataval){
			$sqlstr = 'member_idb = '.$dataval;
		}
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));
//        $data = $this->query('select member_id,member_name from cd_member where xqgl_id = ".session("shop.xqgl_id")." and '.$sqlstr,'mysql');
		return json(['status'=>200,'data'=>$data]);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'ghjl_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['ghjl_id']) throw new ValidateException ('参数错误');
		GhjlModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加记录
 	*/
	public function add(){
		$postField = 'member_id,member_idb,ghjl_time,ghjl_jiesuan,shop_id,xqgl_id,fcxx_id,cewei_id,gh_tui';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ghjl::class);

		$data['ghjl_time'] = !empty($data['ghjl_time']) ? strtotime($data['ghjl_time']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = GhjlModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Ghjl@afterShopAdd',array_merge($data,['ghjl_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  过户回退
 	*/
	public function tuihui(){
		$postField = 'ghjl_id,gh_tui';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Ghjl::class);

		if($ret = hook('hook/Ghjl@beforShopTuihui',$data)){
			return $ret;
		}

		try{
			GhjlModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getTuihuiInfo(){
		$id =  $this->request->post('ghjl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'ghjl_id,gh_tui';
		$res = GhjlModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('cewei_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('cewei_id',explode(',',$list))){
			$data['cewei_ids'] = _generateSelectTree($this->query("select cewei_id as tval,concat_ws('-',b.tccd_name,c.cwqy_name,a.cewei_name) as tkey from cd_cewei as a 

left join cd_tccd as b on a.tccd_id = b.tccd_id 
left join cd_cwqy as c on a.cwqy_id = c.cwqy_id ",'mysql'));
		}
		return $data;
	}


/*start*/
	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getMember_idaa(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("	select member_id,member_name from cd_member where xqgl_id = ".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}
/*end*/


/*start*/
	public function getCewei_id(){
		$member_id =  $this->request->post('member_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = _generateSelectTree($this->query('select cewei_id as tval,concat_ws("-",b.tccd_name,c.cwqy_name,a.cewei_name) as tkey from cd_cewei as a 
left join cd_tccd as b on a.tccd_id = b.tccd_id 
left join cd_cwqy as c on a.cwqy_id = c.cwqy_id 
where member_id ='.$member_id,'mysql'));
		return json($data);
	}

    public function remoteFcxxidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "fcxx_fjbh like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'fcxx_id = '.$dataval;
        }
        $aaa = $this->query('select a.fcxx_id as tval, concat_ws("_",c.louyu_name,b.louyu_name,a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id left join cd_louyu c on b.louyu_pid = c.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is not null and '.$sqlstr,'mysql');
        $bbb = $this->query('select a.fcxx_id as tval, concat_ws("_",b.louyu_name,"商服/车库",a.fcxx_fjbh) as tkey  from cd_fcxx a left join cd_louyu b on a.louyu_id = b.louyu_id where a.xqgl_id='.session("shop.xqgl_id").' and b.louyu_pid is null and '.$sqlstr,'mysql');
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



}

