<?php 
/*
 module:		抄表明细控制器
 create_time:	2023-01-11 21:14:09
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Cbgl as CbglModel;
use think\facade\Db;

class Cbgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','DanHuRuZhang','PiLiangRuZhang'])){
			$idx = $this->request->post('cbgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = CbglModel::find($v);
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
			$where['cbgl_id'] = $this->request->post('cbgl_id', '', 'serach_in');
			$where['a.cbpc_id'] = $this->request->post('cbpc_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['a.louyu_id'] = ['like',$this->request->post('louyu_id', '', 'serach_in')];
			$where['a.fcxx_id'] = ['like',$this->request->post('fcxx_id', '', 'serach_in')];
			$where['a.cbgl_cwyf'] = $this->request->post('cbgl_cwyf', '', 'serach_in');
			$where['a.yblx_id'] = $this->request->post('yblx_id', '', 'serach_in');
			$where['a.ybzl_id'] = $this->request->post('ybzl_id', '', 'serach_in');
			$where['a.cbgl_status'] = $this->request->post('cbgl_status', '', 'serach_in');
			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'cbgl_id desc';

			$sql ="select
a.*,
concat_ws('-',c.louyu_lyqz,c.louyu_name,b.fcxx_fjbh) as fcxx_fjbh,
c.louyu_name,
d.yblx_name,
e.ybzl_name,
f.member_name 
from cd_cbgl as a 
left join cd_fcxx as b on a.fcxx_id = b.fcxx_id 
left join cd_louyu as c on b.louyu_id = c.louyu_id 
left join cd_yblx as d on a.yblx_id = d.yblx_id 
left join cd_ybzl as e on a.ybzl_id = e.ybzl_id 
left join cd_member as f on a.member_id = f.member_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_id,yblx_id,ybzl_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_id(){
		$louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where xqgl_id='.session("shop.xqgl_id").' and louyu_id ='.$louyu_id,'mysql');
		return json($data);
	}


	/*
 	* @Description  单户入账
 	*/
	public function DanHuRuZhang(){
		$idx = $this->request->post('cbgl_id', '', 'serach_in');
		if(empty($idx)) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Cbgl@beforShopDanHuRuZhang',$idx)){
			return $ret;
		}

		$data['cbgl_status'] = 0;
		$res = CbglModel::where(['cbgl_id'=>explode(',',$idx)])->update($data);

		if($ret = hook('hook/Cbgl@afterShopDanHuRuZhang',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  批量入账
 	*/
	public function PiLiangRuZhang(){
		$idx = $this->request->post('cbgl_id', '', 'serach_in');
		if(empty($idx)) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Cbgl@beforShopPiLiangRuZhang',$idx)){
			return $ret;
		}

		$data['cbgl_status'] = 0;
		$res = CbglModel::where(['cbgl_id'=>explode(',',$idx)])->update($data);

		if($ret = hook('hook/Cbgl@afterShopPiLiangRuZhang',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('louyu_id,yblx_id,ybzl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('louyu_id',explode(',',$list))){
			$data['louyu_ids'] = _generateSelectTree($this->query("select louyu_id,louyu_name,louyu_pid from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")."",'mysql'));
		}
		if(in_array('yblx_id',explode(',',$list))){
			$data['yblx_ids'] = $this->query("select yblx_id,yblx_name from cd_yblx",'mysql');
		}
		if(in_array('ybzl_id',explode(',',$list))){
			$data['ybzl_ids'] = $this->query("select ybzl_id,ybzl_name from cd_ybzl where ybzl_pid is not null",'mysql');
		}
		return $data;
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


/*start*/
	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'cbgl_id,cbgl_bqsl,cbgl_sqsl';
		$data = $this->request->only(explode(',',$postField),'post',null);
	
		if(!$data['cbgl_id']) throw new ValidateException ('参数错误');
		
		$cha = Db::name('cbgl')->where('cbgl_id',$data['cbgl_id'])->find();
		if ($cha['cbgl_status']==1){
			return json(['status'=>201,'msg'=>'已入账信息禁止修改']);
		}
        
		CbglModel::update($data);
        if($ret = hook('hook/Cbgl@afterShopBatupdate',array_merge($data))){
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
	/*
 	* @Description  本期用量
 	*/
	public function batupdate(){
		$postField = 'cbgl_id,cbgl_bqsl';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cbgl::class);

		$idx = explode(',',$data['cbgl_id']);

		unset($data['cbgl_id']);

		try{
			CbglModel::where(['cbgl_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
        $data['cbgl_id'] = $idx[0];
        if($ret = hook('hook/Cbgl@afterShopBatupdate',array_merge($data))){
            return $ret;
        }
		return json(['status'=>200,'msg'=>'修改成功']);
	}

    public function CheXiaoRuZhang(){
        $idx = $this->request->post('cbgl_id', '', 'serach_in');
        if(empty($idx)) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Cbgl@beforShopCheXiaoRuZhang',$idx)){ // 未入账
            return $ret;
        }


        $data['cbgl_status'] = 0;

        $res = CbglModel::where(['cbgl_id'=>explode(',',$idx)])->update($data);

        if($ret = hook('hook/Cbgl@afterShopCheXiaoRuZhang',$idx)){ //
            return $ret;
        }

        return json(['status'=>200,'msg'=>'操作成功']);
    }

    /*end*/


    /*
     * @Description  删除
     */
    function delete(){
        $idx =  $this->request->post('cbgl_id', '', 'serach_in');
        if(!$idx) throw new ValidateException ('参数错误');
        CbglModel::destroy(['cbgl_id'=>explode(',',$idx)],true);
        return json(['status'=>200,'msg'=>'操作成功']);
    }
    
}

