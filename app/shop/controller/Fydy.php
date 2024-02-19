<?php 
/*
 module:		费用定义控制器
 create_time:	2023-01-16 14:25:22
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Fydy as FydyModel;
use think\facade\Db;

class Fydy extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('fydy_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = FydyModel::find($v);
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
			$where['fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');

			$where['fydy.shop_id'] = session('shop.shop_id');

			$where['fydy.xqgl_id'] = session('shop.xqgl_id');
			$where['fydy.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');
			$where['fydy.fydy_name'] = ['like',$this->request->post('fydy_name', '', 'serach_in')];
			$where['fydy.fylb_id'] = $this->request->post('fylb_id', '', 'serach_in');
			$where['fydy.jflx_id'] = $this->request->post('jflx_id', '', 'serach_in');

			$field = 'fydy_id,fylx_id,fydy_name,fydy_ysyf,fydy_ysr,fydy_zdr,fydy_ycys,fydy_wyjbl,fydy_px';

			$withJoin = [
				'fylx'=>explode(',','fylx_name'),
				'fylb'=>explode(',','fylb_name'),
				'fydw'=>explode(',','fydw_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fydy_id desc';

			$query = FydyModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('fylx_id,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_cyskxm');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'fydy_id,fydy_px';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['fydy_id']) throw new ValidateException ('参数错误');
		FydyModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,fylx_id,fydy_name,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_kjfp,fydy_ysyf,fydy_ysr,fydy_cyskxm,fydy_zdr,fydy_ycys,fydy_wyjbl,fydy_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fydy::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['fydy_cyskxm'] = implode(',',$data['fydy_cyskxm']);
		$data['fydy_zdr'] = !empty($data['fydy_zdr']) ? strtotime($data['fydy_zdr']) : '';

		try{
			$res = FydyModel::insertGetId($data);
			if($res && empty($data['fydy_px'])){
				FydyModel::update(['fydy_px'=>$res,'fydy_id'=>$res]);
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
		$postField = 'fydy_id,shop_id,xqgl_id,fylx_id,fydy_name,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_kjfp,fydy_ysyf,fydy_ysr,fydy_cyskxm,fydy_zdr,fydy_ycys,fydy_wyjbl,fydy_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fydy::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['fydy_cyskxm'] = implode(',',$data['fydy_cyskxm']);
		$data['fydy_zdr'] = !empty($data['fydy_zdr']) ? strtotime($data['fydy_zdr']) : '';

		try{
			FydyModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('fydy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fydy_id,shop_id,xqgl_id,fylx_id,fydy_name,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_kjfp,fydy_ysyf,fydy_ysr,fydy_cyskxm,fydy_zdr,fydy_ycys,fydy_wyjbl,fydy_remarks';
		$res = FydyModel::field($field)->find($id);
		$res['fydy_cyskxm'] = explode(',',$res['fydy_cyskxm']);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
    function delete(){
        $idx =  $this->request->post('fydy_id', '', 'serach_in');
        if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Fydy@beforShopDelete',$idx)){
            return $ret;
        }

        FydyModel::destroy(['fydy_id'=>explode(',',$idx)],true);

        if($ret = hook('hook/Fydy@afterShopDelete',$idx)){
            return $ret;
        }

        return json(['status'=>200,'msg'=>'操作成功']);
    }


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('fydy_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fydy_id,fylx_id,fydy_name,fydy_ysyf,fydy_ysr,fydy_zdr,fydy_ycys,fydy_wyjbl,fydy_px';
		$res = FydyModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('fylx_id,fylb_id,fydw_id,jflx_id,qzfs_id,fydy_cyskxm')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		if(in_array('fylb_id',explode(',',$list))){
			$data['fylb_ids'] = $this->query("select fylb_id,fylb_name from cd_fylb",'mysql');
		}
		if(in_array('fydw_id',explode(',',$list))){
			$data['fydw_ids'] = $this->query("select fydw_id,fydw_name from cd_fydw",'mysql');
		}
		if(in_array('jflx_id',explode(',',$list))){
			$data['jflx_ids'] = $this->query("select jflx_id,jflx_name from cd_jflx",'mysql');
		}
		if(in_array('qzfs_id',explode(',',$list))){
			$data['qzfs_ids'] = $this->query("select qzfs_id,qzfs_name from cd_qzfs",'mysql');
		}
		if(in_array('fydy_cyskxm',explode(',',$list))){
			$data['fydy_cyskxms'] = $this->query("select fydy_id,fydy_name from cd_fydy where fylx_id<>3",'mysql');
		}
		return $data;
	}



}

