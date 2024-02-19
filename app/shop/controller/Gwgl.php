<?php 
/*
 module:		岗位管理控制器
 create_time:	2023-10-13 16:33:40
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Gwgl as GwglModel;
use think\facade\Db;

class Gwgl extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete'])){
			$idx = $this->request->post('gwgl_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = GwglModel::find($v);
					if($info['shop_id'] <> session('shop.shop_id')){
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
			$where['gwgl_id'] = $this->request->post('gwgl_id', '', 'serach_in');

			$where['gwgl.shop_id'] = session('shop.shop_id');
			$where['gwgl.gwgl_gwmc'] = ['like',$this->request->post('gwgl_gwmc', '', 'serach_in')];
			$where['gwgl.xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');

			$field = 'gwgl_id,gwgl_gwmc,gwgl_px,gwgl_sjgw';

			$withJoin = [
				'xqgl'=>explode(',','xqgl_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'gwgl_px asc';

			$query = GwglModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['gwgl_id','gwgl_sjgw']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('xqgl_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getGwgl_sjgw(){
		$xqgl_id =  $this->request->post('xqgl_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = _generateSelectTree($this->query('select gwgl_id,gwgl_gwmc,gwgl_sjgw from cd_gwgl where shop_id = '.session('shop.shop_id').' and xqgl_id = '.$xqgl_id,'mysql'));
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'gwgl_id,gwgl_px';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['gwgl_id']) throw new ValidateException ('参数错误');
		GwglModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,gwgl_gwmc,xqgl_id,gwgl_sjgw,gwgl_px,gwgl_role';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gwgl::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['gwgl_role'] = implode(',',$data['gwgl_role']);

		try{
			$res = GwglModel::insertGetId($data);
			if($res && empty($data['gwgl_px'])){
				GwglModel::update(['gwgl_px'=>$res,'gwgl_id'=>$res]);
			}
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'gwgl_id,shop_id,gwgl_gwmc,xqgl_id,gwgl_sjgw,gwgl_px,gwgl_role';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gwgl::class);

		$data['shop_id'] = session('shop.shop_id');

		if(!isset($data['gwgl_sjgw'])){
			$data['gwgl_sjgw'] = null;
		}
		$data['gwgl_role'] = implode(',',$data['gwgl_role']);

		try{
			GwglModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('gwgl_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'gwgl_id,shop_id,gwgl_gwmc,xqgl_id,gwgl_sjgw,gwgl_px,gwgl_role';
		$res = GwglModel::field($field)->find($id);
		$res['gwgl_role'] = explode(',',$res['gwgl_role']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  权限节点
 	*/
//	function getRoleAccess(){
//		$nodes = (new \utils\AuthAccess())->getNodeMenus(298,0);
//
//		$app_name = Db::name("application")->where("app_id",298)->value('app_dir');
//
//		if($baseNode = hook('app/'.$app_name.'/hook/Base@getNode',$nodes)){
//			$nodes = array_merge($baseNode,$nodes);
//		}
//
//		array_multisort(array_column($nodes, 'sortid'),SORT_ASC,$nodes );
//		return json(['status'=>200,'menus'=>$nodes]);
//	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('gwgl_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		GwglModel::destroy(['gwgl_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('xqgl_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('xqgl_id',explode(',',$list))){
			$data['xqgl_ids'] = $this->query("select xqgl_id,xqgl_name from cd_xqgl where shop_id=".session("shop.shop_id")."",'mysql');
		}
		return $data;
	}

 /*start*/
	/*
 	* @Description  权限节点
 	*/
	function getRoleAccess(){
	    $nodess = (new \utils\AuthAccess())->getNodeMenus(298,0);
	    //根据购买获取菜单
        $gouma = Db::name('shop')->alias('a')
            ->field('a.*,b.access')
            ->join('shoprole b', 'a.goumai = b.role_id')
            ->where('a.shop_id',session('shop.shop_id'))
            ->find();

        $gouma = explode(',',$gouma['access']);

        $nodes = $this->designData($nodess,$gouma);

		if($baseNode = hook('hook/Base@getShopNode',$nodes)){
			$nodes = array_merge($baseNode,$nodes);
		}

		array_multisort(array_column($nodes, 'sortid'),SORT_ASC,$nodes );
		return json(['status'=>200,'menus'=>$nodes]);
	}
    /*end*/

	/*start*/

    private function designData($nodess,$gouma) {
        $nodes = [];

        foreach ($nodess as $nodess_key => $nodess_item) {

            if (in_array($nodess_item['access'],$gouma)) {

                $nodes[$nodess_key] = $nodess_item;

                if (!empty($nodess_item['children'])) {

                    $nodes[$nodess_key]['children'] = $this->designData($nodess_item['children'],$gouma);
                }
            }
        }

        return $nodes;

    }

	/*end*/



}

