<?php 
/*
 module:		分配房间控制器
 create_time:	2023-01-05 11:28:38
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Fyfp as FyfpModel;
use think\facade\Db;

class Fyfp extends Admin {


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
			$where['fyfp_id'] = $this->request->post('fyfp_id', '', 'serach_in');
			$where['fyfp.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['fyfp.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['fyfp.louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['fyfp.fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
			$where['fyfp.fwlx_id'] = $this->request->post('fwlx_id', '', 'serach_in');

			$field = 'fyfp_id,fyfp_fzxs,fyfp_znj';

			$withJoin = [
				'fcxx'=>explode(',','fcxx_fjbh'),
				'fybz'=>explode(',','fybz_name,fybz_bzdj'),
				'fydy'=>explode(',','fydy_name'),
				'louyu'=>explode(',','louyu_id,louyu_pid,louyu_name'),
				'fwlx'=>explode(',','fwlx_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fyfp_id desc';

			$query = FyfpModel::field($field);

			$re = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();
            $res = $re;

            foreach ($re['data'] as $re_key => $re_data) {

                if (!empty($re_data['louyu']['louyu_pid'])) {
                    $louyu_name = Db::name('louyu')
                        ->where('louyu_id',$re_data['louyu']['louyu_pid'])
                        ->value('louyu_name');
                } else {
                    $louyu_name = Db::name('louyu')
                        ->where('louyu_id',$re_data['louyu']['louyu_id'])
                        ->value('louyu_name');
                }
                $res['data'][$re_key]['louyu']['louyu_name'] = $louyu_name .'-'.$re_data['louyu']['louyu_name']; // 单元
            }

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_id,fwlx_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_id(){
		$louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where louyu_id ='.$louyu_id,'mysql');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'fyfp_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['fyfp_id']) throw new ValidateException ('参数错误');
		FyfpModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  单独分配
 	*/
	public function add(){
		$postField = 'louyu_id,fcxx_id,fydy_id,fybz_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fyfp::class);

        if($ret = hook('hook/Fyfp@beforShopAdd',$data)){
            return $ret;
        }

        try{
			$res = FyfpModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Fyfp@afterShopAdd',array_merge($data,['fyfp_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  去除分配
 	*/
	function delete(){
		$idx =  $this->request->post('fyfp_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		FyfpModel::destroy(['fyfp_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('louyu_id,fwlx_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('louyu_id',explode(',',$list))){
			$data['louyu_ids'] = _generateSelectTree($this->query("select louyu_id,louyu_name,louyu_pid from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")."",'mysql'));
		}
		if(in_array('fwlx_id',explode(',',$list))){
			$data['fwlx_ids'] = $this->query("select fwlx_id,fwlx_name from cd_fwlx",'mysql');
		}
		return $data;
	}

	/*start*/
	/*
 	* @Description  批量分配
 	*/
	public function plAdd(){
		$postField = 'louyu_id,fydy_id,fybz_id,fwlx_id,start_loucen,end_loucen,zheng_fu';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fyfp::class);

		if($ret = hook('hook/Fyfp@beforShopPlAdd',$data)){
			return $ret;
		}

		/*try{
			$res = FyfpModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);*/
	}

	public function getLouyuid() {

	    $data['louyu_ids'] = $this->query("select louyu_id,louyu_name from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and louyu_pid is null",'mysql');

	    $data['fwlx_ids'] = $this->query("select fwlx_id,fwlx_name from cd_fwlx",'mysql');

        return json(['status'=>200,'data'=>$data]);
    }


	/*end*/



}

