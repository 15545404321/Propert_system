<?php 
/*
 module:		楼宇信息控制器
 create_time:	2023-01-09 15:05:06
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Louyu as LouyuModel;
use think\facade\Db;

class Louyu extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','jiaDy','jiaLc','delete'])){
			$idx = $this->request->post('louyu_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = LouyuModel::find($v);
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
			$where['louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'louyu_id asc';

			$sql ="select a.*,b.louyusx_name,c.louyutype_name from cd_louyu as a
left join cd_louyusx as b on a.louyusx_id = b.louyusx_id
left join cd_louyutype as c on a.louyutype_id = c.louyutype_id
where louyu_pid is Null";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$res['data'] = _generateListTree($res['data'],0,['louyu_id','louyu_pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_pid,louyutype_id,louyusx_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'louyu_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['louyu_id']) throw new ValidateException ('参数错误');
		LouyuModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加楼宇
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,louyu_name,louyutype_id,louyusx_id,louyu_dysl,louyu_lczs,louyu_chzs,louyu_flcs,louyu_jzsj,louyu_jzmj,louyu_jsdw,louyu_dtsl,louyu_dscs,louyu_ycjh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Louyu::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['louyu_jzsj'] = !empty($data['louyu_jzsj']) ? strtotime($data['louyu_jzsj']) : '';

		try{
			$res = LouyuModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Louyu@afterShopAdd',array_merge($data,['louyu_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改楼宇
 	*/
	public function update(){
		$postField = 'louyu_id,louyu_name,louyutype_id,louyusx_id,louyu_jzsj,louyu_jzmj,louyu_jsdw,louyu_dtsl';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Louyu::class);

		$data['louyu_jzsj'] = !empty($data['louyu_jzsj']) ? strtotime($data['louyu_jzsj']) : '';

		try{
			LouyuModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

        if($ret = hook('hook/Louyu@afterShopUpdate',$data)) {
            return $ret;
        }

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('louyu_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'louyu_id,louyu_name,louyutype_id,louyusx_id,louyu_jzsj,louyu_jzmj,louyu_jsdw,louyu_dtsl';
		$res = LouyuModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  追加单元
 	*/
	public function jiaDy(){
		$postField = 'louyu_id,louyu_dysl';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['louyu_id'])) throw new ValidateException ('参数错误');
		if(empty($data['louyu_dysl'])) throw new ValidateException ('值不能为空');
		$res = LouyuModel::field('louyu_dysl')->where('louyu_id',$data['louyu_id'])->inc('louyu_dysl',$data['louyu_dysl'])->update();

		if($ret = hook('hook/Louyu@afterShopJiaDy',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  追加楼层
 	*/
	public function jiaLc(){
		$postField = 'louyu_id,louyu_lczs';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(empty($data['louyu_id'])) throw new ValidateException ('参数错误');
		if(empty($data['louyu_lczs'])) throw new ValidateException ('值不能为空');

		if($ret = hook('hook/Louyu@beforShopJiaLc',$data)){
			return $ret;
		}

		$res = LouyuModel::field('louyu_lczs')->where('louyu_id',$data['louyu_id'])->inc('louyu_lczs',$data['louyu_lczs'])->update();

		if($ret = hook('hook/Louyu@afterShopJiaLc',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  删除楼宇
 	*/
	function delete(){
		$idx =  $this->request->post('louyu_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Louyu@beforShopDelete',$idx)){
			return $ret;
		}

		LouyuModel::destroy(['louyu_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Louyu@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('louyu_pid,louyutype_id,louyusx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('louyu_pid',explode(',',$list))){
			$data['louyu_pids'] = _generateSelectTree($this->query("select louyu_id,louyu_name,louyu_pid from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and louyu_pid is Null",'mysql'));
		}
		if(in_array('louyutype_id',explode(',',$list))){
			$data['louyutype_ids'] = $this->query("select louyutype_id,louyutype_name from cd_louyutype",'mysql');
		}
		if(in_array('louyusx_id',explode(',',$list))){
			$data['louyusx_ids'] = $this->query("select louyusx_id,louyusx_name from cd_louyusx",'mysql');
		}
		return $data;
	}



}

