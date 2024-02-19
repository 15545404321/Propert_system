<?php 
/*
 module:		首页装修控制器
 create_time:	2023-05-06 12:00:33
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Renovation as RenovationModel;
use think\facade\Db;

class Renovation extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('renovation_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = RenovationModel::find($v);
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
			$where['renovation_id'] = $this->request->post('renovation_id', '', 'serach_in');
			$where['renovation_name'] = $this->request->post('renovation_name', '', 'serach_in');
			$where['renovation_page'] = $this->request->post('renovation_page', '', 'serach_in');
			$where['renovation_place'] = $this->request->post('renovation_place', '', 'serach_in');
			$where['renovation_type'] = $this->request->post('renovation_type', '', 'serach_in');
			$where['renovation_synopsis'] = $this->request->post('renovation_synopsis', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$field = 'renovation_id,renovation_name,renovation_image,renovation_page,renovation_synopsis';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'renovation_id desc';

			$query = RenovationModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['renovation_page']){
					$res['data'][$k]['renovation_page'] = Db::query("select decoconfig_name from  cd_decoconfig where decoconfig_id=".$v['renovation_page']."")[0]['decoconfig_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('renovation_page,renovation_type');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'renovation_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['renovation_id']) throw new ValidateException ('参数错误');
		RenovationModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'renovation_name,renovation_image,renovation_page,renovation_place,renovation_type,renovation_synopsis,renovation_extra,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Renovation::class);

		$data['renovation_extra'] = getItemData($data['renovation_extra']);
		$data['shop_id'] = session('shop.shop_id');

		try{
			$res = RenovationModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'renovation_id,renovation_name,renovation_image,renovation_page,renovation_place,renovation_type,renovation_synopsis,renovation_extra,shop_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Renovation::class);

		$data['renovation_extra'] = getItemData($data['renovation_extra']);
		$data['shop_id'] = session('shop.shop_id');

		try{
			RenovationModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('renovation_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'renovation_id,renovation_name,renovation_image,renovation_page,renovation_place,renovation_type,renovation_synopsis,renovation_extra,shop_id';
		$res = RenovationModel::field($field)->find($id);
		$res['renovation_extra'] = json_decode($res['renovation_extra'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('renovation_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		RenovationModel::destroy(['renovation_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('renovation_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'renovation_id,renovation_name,renovation_image,renovation_page,renovation_synopsis';
		$res = RenovationModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		if($res['renovation_page']){
			$res['renovation_page'] = Db::query("select decoconfig_name from  cd_decoconfig where decoconfig_id=".$res['renovation_page']."")[0]['decoconfig_name'];
		}
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('renovation_page,renovation_type')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('renovation_page',explode(',',$list))){
			$data['renovation_pages'] = $this->query("select decoconfig_id,decoconfig_name from cd_decoconfig",'mysql');
		}
		if(in_array('renovation_type',explode(',',$list))){
			$data['renovation_types'] = $this->query("select indexmodule_id,indexmodule_name from cd_indexmodule",'mysql');
		}
		return $data;
	}

	/*start*/
	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getRenovation_place(){
		$renovation_page =  $this->request->post('renovation_page', '', 'serach_in');
		$data['status'] = 200;

		$shop_id = session('shop.shop_id');
		$xqgl_id = session('shop.xqgl_id');

		$where = ' where shop_id = '.$shop_id.' and xqgl_id ='.$xqgl_id;

		switch ($renovation_page) {
            case '2': // 便民电话
                $data['data'] = $this->query(
                    'select dhfl_id,dhfl_name from cd_dhfl'.$where,'mysql');
                break;
            case '4': // 物业新闻
                $data['data'] = $this->query(
                    'select wzfl_id,wzfl_name from cd_wzfl'.$where,'mysql');
                break;
            default :
                $data['data'] = [];
//                break;
        }
        
		return json($data);
	}
    /*end*/



}

