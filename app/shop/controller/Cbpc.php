<?php 
/*
 module:		抄表管理控制器
 create_time:	2023-01-28 12:59:22
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Cbpc as CbpcModel;
use think\facade\Db;

class Cbpc extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail','Plrz'])){
			$idx = $this->request->post('cbpc_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = CbpcModel::find($v);
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
			$where['cbpc_id'] = $this->request->post('cbpc_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['yblx_id'] = $this->request->post('yblx_id', '', 'serach_in');
			$where['ybzl_id'] = $this->request->post('ybzl_id', '', 'serach_in');
			$where['cbpc_status'] = $this->request->post('cbpc_status', '', 'serach_in');

			$field = 'cbpc_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,louyu_id,yblx_id,ybzl_id,cbpc_status';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'cbpc_id desc';

			$query = CbpcModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['louyu_id']){
					$res['data'][$k]['louyu_id'] = Db::query("select louyu_name from  cd_louyu where louyu_pid is null and shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")." and louyu_id=".$v['louyu_id']."")[0]['louyu_name'];
				}
				if($v['yblx_id']){
					$res['data'][$k]['yblx_id'] = Db::query("select yblx_name from  cd_yblx where yblx_id=".$v['yblx_id']."")[0]['yblx_name'];
				}
				if($v['ybzl_id']){
					$res['data'][$k]['ybzl_id'] = Db::query("select ybzl_name from  cd_ybzl where ybzl_pid is not null and ybzl_id=".$v['ybzl_id']."")[0]['ybzl_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_id,yblx_id,ybzl_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'cbpc_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['cbpc_id']) throw new ValidateException ('参数错误');
		CbpcModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  生成抄表
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,louyu_id,yblx_id,ybzl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cbpc::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['cbpc_kstime'] = !empty($data['cbpc_kstime']) ? strtotime($data['cbpc_kstime']) : '';
		$data['cbpc_jstime'] = !empty($data['cbpc_jstime']) ? strtotime($data['cbpc_jstime']) : '';

		if($ret = hook('hook/Cbpc@beforShopAdd',$data)){
			return $ret;
		}

		try{
		    $res = CbpcModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Cbpc@afterShopAdd',array_merge($data,['cbpc_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}

/*start*/
    /*
     * @Description  单户抄表
     */
    public function aloneAdd(){
        $postField = 'shop_id,xqgl_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,louyu_id,danyuan_id,fcxx_id,yblx_id,ybzl_id,cbpc_ghcb';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');
        $data['cbpc_kstime'] = !empty($data['cbpc_kstime']) ? strtotime($data['cbpc_kstime']) : '';
        $data['cbpc_jstime'] = !empty($data['cbpc_jstime']) ? strtotime($data['cbpc_jstime']) : '';

        if($ret = hook('hook/Cbpc@beforShopAloneAdd',$data)){
            return $ret;
        }

        try{
            $res = CbpcModel::insertGetId($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }

        if($ret = hook('hook/Cbpc@afterShopAloneAdd',array_merge($data,['cbpc_id'   => $res]))){
            return $ret;
        }

        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
    }
/*end*/

	/*
 	* @Description  重新生成
 	*/
	public function update(){
		$postField = 'cbpc_id,shop_id,xqgl_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,yblx_id,ybzl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Cbpc::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['cbpc_kstime'] = !empty($data['cbpc_kstime']) ? strtotime($data['cbpc_kstime']) : '';
		$data['cbpc_jstime'] = !empty($data['cbpc_jstime']) ? strtotime($data['cbpc_jstime']) : '';

		if($ret = hook('hook/Cbpc@beforShopUpdate',$data)){
			return $ret;
		}

		try{
		    CbpcModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Cbpc@afterShopUpdate',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('cbpc_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cbpc_id,shop_id,xqgl_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,yblx_id,ybzl_id';
		$res = CbpcModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('cbpc_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Cbpc@beforShopDelete',$idx)){
            return $ret;
        }

        CbpcModel::destroy(['cbpc_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Cbpc@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('cbpc_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'cbpc_id,cbpc_cwyf,cbpc_kstime,cbpc_jstime,louyu_id,yblx_id,ybzl_id,cbpc_status';
		$res = CbpcModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  操作入账
 	*/
	public function Plrz(){
		$idx = $this->request->post('cbpc_id', '', 'serach_in');
		if(empty($idx)) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Cbpc@beforShopPlrz',$idx)){
			return $ret;
		}

		$data['cbpc_status'] = 1;
		$res = CbpcModel::where(['cbpc_id'=>explode(',',$idx)])->update($data);

		if($ret = hook('hook/Cbpc@afterShopPlrz',$idx)){
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
			$data['louyu_ids'] = $this->query("select louyu_id,louyu_name from cd_louyu where louyu_pid is null and shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('yblx_id',explode(',',$list))){
			$data['yblx_ids'] = $this->query("select yblx_id,yblx_name from cd_yblx",'mysql');
		}
		if(in_array('ybzl_id',explode(',',$list))){
			$data['ybzl_ids'] = $this->query("select ybzl_id,ybzl_name from cd_ybzl where ybzl_pid is not null",'mysql');
		}
		return $data;
	}

/*start*/
    function getDanyuan_id(){
        $louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select louyu_id,louyu_name from cd_louyu where xqgl_id='.session('shop.xqgl_id').' and '.'(louyu_pid ='.$louyu_id.' or louyu_id ='.$louyu_id.')','mysql');
        return json($data);
    }

    function getFcxx_id() {
        $danyuan_id =  $this->request->post('danyuan_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where xqgl_id='.session('shop.xqgl_id').' and louyu_id ='.$danyuan_id,'mysql');
        return json($data);
    }
/*end*/

}

