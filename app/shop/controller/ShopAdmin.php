<?php 
/*
 module:		员工管理控制器
 create_time:	2023-01-06 11:13:05
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\ShopAdmin as ShopAdminModel;
use think\facade\Db;

class ShopAdmin extends Admin {


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
			$where['shop_admin_id'] = $this->request->post('shop_admin_id', '', 'serach_in');
			$where['a.cname'] = $this->request->post('cname', '', 'serach_in');
			$where['a.xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');
			$where['a.zzgl_id'] = $this->request->post('zzgl_id', '', 'serach_in');
			$where['a.gwgl_id'] = $this->request->post('gwgl_id', '', 'serach_in');
			$where['a.account'] = $this->request->post('account', '', 'serach_in');
			$where['a.tel'] = ['like',$this->request->post('tel', '', 'serach_in')];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'shop_admin_id desc';

			$sql ="select a.*,zz.zzgl_bmmc,gw.gwgl_gwmc,c.xqgl_name from cd_shop_admin a 
left join cd_zzgl zz on a.zzgl_id=zz.zzgl_id 
left join cd_gwgl gw on a.gwgl_id=gw.gwgl_id 
left join cd_xqgl c on a.xqgl_id=c.xqgl_id
where a.root=0 and a.shop_id=".session("shop.shop_id")."";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('xqgl_id,gwgl_id,xqgl_ids');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getZzgl_id(){
		$xqgl_id =  $this->request->post('xqgl_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = _generateSelectTree($this->query('select zzgl_id,zzgl_bmmc,zzgl_sjbm from cd_zzgl where shop_id='.session('shop.shop_id').' and xqgl_id ='.$xqgl_id,'mysql'));
		return json($data);
	}


	/*
 	* @Description  添加员工
 	*/
	public function add(){
        $postField = 'shop_id,root,cname,xqgl_id,zzgl_id,gwgl_id,account,password,tel,create_time,disable,xqgl_ids';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $this->validate($data,\app\shop\validate\ShopAdmin::class);

        $data['shop_id'] = session('shop.shop_id');
        $data['password'] = md5($data['password'].config('my.password_secrect'));
        $data['create_time'] = time();
        $data['xqgl_ids'] = implode(',',$data['xqgl_ids']);

        try{
            $res = ShopAdminModel::insertGetId($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }
        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  复制添加
 	*/
	public function copydata(){
        return json(['status'=>-200,'msg'=>'此为演示版，请联系九福网络获取正版授权，联系方式：18945406222']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getCopydataInfo(){
		$id =  $this->request->post('shop_admin_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_admin_id,shop_id,root,cname,xqgl_id,zzgl_id,gwgl_id,account,tel,create_time,update_time,disable,xqgl_ids';
		$res = ShopAdminModel::field($field)->find($id);
		$res['xqgl_ids'] = explode(',',$res['xqgl_ids']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
        $postField = 'shop_admin_id,shop_id,cname,xqgl_id,zzgl_id,gwgl_id,account,tel,update_time,disable,xqgl_ids';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $this->validate($data,\app\shop\validate\ShopAdmin::class);

        $data['shop_id'] = session('shop.shop_id');

        if(!isset($data['zzgl_id'])){
            $data['zzgl_id'] = null;
        }

        if(!isset($data['gwgl_id'])){
            $data['gwgl_id'] = null;
        }
        $data['update_time'] = time();
        $data['xqgl_ids'] = implode(',',$data['xqgl_ids']);

        try{
            ShopAdminModel::update($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }

        if($ret = hook('hook/ShopAdmin@afterShopUpdate',$data)){
            return $ret;
        }

        return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('shop_admin_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_admin_id,shop_id,cname,xqgl_id,zzgl_id,gwgl_id,account,tel,update_time,disable,xqgl_ids';
		$res = ShopAdminModel::field($field)->find($id);
		$res['xqgl_ids'] = explode(',',$res['xqgl_ids']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  重置密码
 	*/
	public function resetPwd(){
		$postField = 'shop_admin_id,password';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['shop_admin_id'])) throw new ValidateException ('参数错误');
		if(empty($data['password'])) throw new ValidateException ('密码不能为空');

		$data['password'] = md5($data['password'].config('my.password_secrect'));
		$res = ShopAdminModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


/*start*/
/*
 * @Description  获取定义sql语句的字段信息
 */
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('xqgl_id,gwgl_id,zzgl_id,xqgl_ids')]);
	}
/*end*/


/*start*/
/*
 * @Description  获取定义sql语句的字段信息
 */
	private function getSqlField($list){
		$data = [];
		if(in_array('xqgl_id',explode(',',$list))){
			$data['xqgl_ids'] = $this->query("select xqgl_id,xqgl_name from cd_xqgl where shop_id=".session("shop.shop_id")."",'mysql');
		}
		if(in_array('gwgl_id',explode(',',$list))){
			$data['gwgl_ids'] = _generateSelectTree($this->query("select gwgl_id,gwgl_gwmc,gwgl_sjgw from cd_gwgl where shop_id = ".session("shop.shop_id")."",'mysql'));
		}
		if(in_array('zzgl_id',explode(',',$list))){
			$data['zzgl_ids'] = _generateSelectTree($this->query("select zzgl_id,zzgl_bmmc,zzgl_sjbm from cd_zzgl where shop_id = ".session("shop.shop_id")."",'mysql'));
		}
		if(in_array('xqgl_ids',explode(',',$list))){
			$data['xqgl_idss'] = $this->query("select xqgl_id,xqgl_name from cd_xqgl where shop_id=".session("shop.shop_id")."",'mysql');
		}
		return $data;
	}
/*end*/


/*start*/
    public function getGwgl_id(){
        $xqgl_id =  $this->request->post('xqgl_id', '', 'serach_in');
        $data['status'] = 200;
        $data['data'] = _generateSelectTree($this->query('select gwgl_id,gwgl_gwmc,gwgl_sjgw from cd_gwgl where shop_id='.session('shop.shop_id').' and xqgl_id ='.$xqgl_id,'mysql'));
        return json($data);
    }

/*end*/



}

