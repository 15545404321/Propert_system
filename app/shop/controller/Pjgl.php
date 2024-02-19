<?php 
/*
 module:		票据管理控制器
 create_time:	2023-01-18 11:56:05
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Pjgl as PjglModel;
use think\facade\Db;

class Pjgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('pjgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = PjglModel::find($v);
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
			$where['pjgl_id'] = $this->request->post('pjgl_id', '', 'serach_in');
			$where['pjgl.pjlx_id'] = $this->request->post('pjlx_id', '', 'serach_in');
			$where['pjgl.pjlx_pid'] = $this->request->post('pjlx_pid', '', 'serach_in');
			$where['pjgl.pjgl_name'] = ['like',$this->request->post('pjgl_name', '', 'serach_in')];

			$where['pjgl.shop_id'] = session('shop.shop_id');

			$where['pjgl.xqgl_id'] = session('shop.xqgl_id');

			$field = 'pjgl_id,pjlx_id,pjlx_pid,pjgl_name,pjgl_qsbm,pjgl_pjzs,pjgl_time,pjgl_status';

			$withJoin = [
				'pjlx'=>explode(',','pjlx_name'),
				'shopadmin'=>explode(',','cname'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'pjgl_id desc';

			$query = PjglModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('pjlx_id');
			return json($data);
		}
	}



	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'pjlx_id,pjlx_pid,pjgl_name,pjgl_qsbm,pjgl_pjzs,shop_admin_id,pjgl_time,pjgl_status,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Pjgl::class);

		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['pjgl_time'] = time();
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = PjglModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

        if($ret = hook('hook/Pjgl@afterShopAdd',array_merge($data,['pjgl_id'=>$res]))){
            return $ret;
        }

        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'pjgl_id,pjlx_id,pjlx_pid,pjgl_name,pjgl_qsbm,pjgl_pjzs,shop_admin_id,pjgl_time,pjgl_status,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Pjgl::class);

		$data['shop_admin_id'] = session('shop.shop_admin_id');
		$data['pjgl_time'] = !empty($data['pjgl_time']) ? strtotime($data['pjgl_time']) : '';
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			PjglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

        if($ret = hook('hook/Pjgl@afterShopUpdate',$data)){
            return $ret;
        }

        return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('pjgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjgl_id,pjlx_id,pjlx_pid,pjgl_name,pjgl_qsbm,pjgl_pjzs,shop_admin_id,pjgl_time,pjgl_status,shop_id,xqgl_id';
		$res = PjglModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('pjgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
        if($ret = hook('hook/Pjgl@beforShopDelete',$idx)){
            return $ret;
        }
		PjglModel::destroy(['pjgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('pjgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'pjgl_id,pjlx_id,pjlx_pid,pjgl_name,pjgl_qsbm,pjgl_pjzs,pjgl_time,pjgl_status';
		$res = PjglModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('pjlx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('pjlx_id',explode(',',$list))){
			$data['pjlx_ids'] = $this->query("select pjlx_id,pjlx_name from cd_pjlx where pjlx_pid is Null",'mysql');
		}
		return $data;
	}


/*start*/
	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getPjlx_pid(){
		$pjlx_id =  $this->request->post('pjlx_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select pjlx_id,pjlx_name from cd_pjlx where pjlx_pid ='.$pjlx_id,'mysql');
		return json($data);
	}

    /*
     * @Description  修改排序开关
     */
    function updateExt(){
        $postField = 'pjgl_id,pjgl_status';
        $data = $this->request->only(explode(',',$postField),'post',null);
        if(!$data['pjgl_id']) throw new ValidateException ('参数错误');
        PjglModel::update($data);

        if ($data['pjgl_status'] == 1) {

            $pjlx_id = Db::name('pjgl')->where('pjgl_id',$data['pjgl_id'])->value('pjlx_id');

            Db::name('pjgl')
                ->where('pjlx_id',$pjlx_id)
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('pjgl_id','<>',$data['pjgl_id'])
                ->update(['pjgl_status' => 0]);
        }

        return json(['status'=>200,'msg'=>'操作成功']);
    }
/*end*/



}

