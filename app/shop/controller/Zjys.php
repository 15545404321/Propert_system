<?php 
/*
 module:		追加应收控制器
 create_time:	2023-01-13 19:58:22
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zjys as ZjysModel;
use think\facade\Db;

class Zjys extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('zjys_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = ZjysModel::find($v);
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
			$where['a.fcxx_id'] = ['like',$this->request->post('fcxx_id', '', 'serach_in')];
			$where['a.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['a.zjys_zjzy'] = ['like',$this->request->post('zjys_zjzy', '', 'serach_in')];

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zjys_id desc';

			$sql ="select 
a.*,
concat_ws('-',z.louyu_lyqz,z.louyu_name,b.fcxx_fjbh) as fcxx_fjbh,
c.fydy_name,
d.fybz_name,
e.member_name

from cd_zjys as a 
left join cd_fcxx b on a.fcxx_id = b.fcxx_id 
left join cd_fydy c on a.fydy_id = c.fydy_id 
left join cd_fybz d on a.fybz_id = d.fybz_id 
left join cd_member e on a.member_id = e.member_id 
left join cd_louyu z on z.louyu_id = b.louyu_id ";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fydy_id,fylx_id');
			return json($data);
		}
	}

	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'zjys_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['zjys_id']) throw new ValidateException ('参数错误');
		ZjysModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  追加应收
 	*/
	public function add(){
		$postField = 'fcxx_id,member_id,fydy_id,fybz_id,zjys_dcys,zjys_sysl,zjys_bcys,zjys_ktime,zjys_jtime,zjys_zjzy,shop_id,xqgl_id,fylx_id';
		$data = $this->request->only(explode(',',$postField),'post',null);
		//根据费用定义fydy查询fylx//////////////////////////////爽加2023年1月15日23:15:41
//		$fydy = Db::name('fydy')->where('fydy_id',$data['fydy_id'])->field('fylx_id')->find();

		$this->validate($data,\app\shop\validate\Zjys::class);

		$data['zjys_ktime'] = !empty($data['zjys_ktime']) ? strtotime($data['zjys_ktime']) : '';
		$data['zjys_jtime'] = !empty($data['zjys_jtime']) ? strtotime($data['zjys_jtime']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
//		$data['fylx_id'] = $fydy['fylx_id'];

		try{
			$res = ZjysModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Zjys@afterShopAdd',array_merge($data,['zjys_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}

	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zjys_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Zjys@beforShopDelete',$idx)){
			return $ret;
		}

		ZjysModel::destroy(['zjys_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Zjys@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fydy_id,fylx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fydy_id',explode(',',$list))){
			$data['fydy_ids'] = $this->query("select fydy_id,fydy_name from cd_fydy where (fylx_id=3 or fylx_id=4 or fylx_id=5) and xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		return $data;
	}

	/*start*/

    /*
    * @Description  获取定义sql语句的字段信息
    */
    public function getFybz_id(){
        $fydy_id =  $this->request->post('fydy_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select fybz_id,fybz_name from cd_fybz where fydy_id ='.$fydy_id,'mysql');
        $data['fylx_id'] = Db::name('fydy')->where('fydy_id',$fydy_id)->value('fylx_id');
        return json($data);
    }

    /*
    * @Description  获取远程搜索字段信息
    */
    public function remoteFcxxidList(){
        $queryString = $this->request->post('queryString');

        $queryArray = explode(',',$queryString);

        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "a.fcxx_fjbh like '".$queryArray[0]."%' and a.member_id=".$queryArray[1];
        }
        if($dataval){
            $sqlstr = 'a.fcxx_id = '.$dataval;
        }
        $data = $this->query('select fcxx_id as tval,concat_ws("_",b.louyu_lyqz,b.louyu_name,a.fcxx_fjbh) as tkey from cd_fcxx a left join cd_louyu b on a.louyu_id=b.louyu_id where a.xqgl_id='.session('shop.xqgl_id').' and a.shop_id='.session('shop.shop_id').' and '.$sqlstr,'mysql');
        return json(['status'=>200,'data'=>$data]);
    }
    /*end*/



}

