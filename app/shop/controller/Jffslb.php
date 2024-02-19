<?php 
/*
 module:		计费方式列表控制器
 create_time:	2023-01-10 09:42:12
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Jffslb as JffslbModel;
use think\facade\Db;

class Jffslb extends Admin {


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
			$where['fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['jffslb.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['jffslb.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');
			$where['jffslb.fybz_name'] = ['like',$this->request->post('fybz_name', '', 'serach_in')];

			$field = 'fybz_id,fydy_id,fylx_id,fybz_name,fybz_bzdj,fybz_jfxs,fybz_hzl,fybz_status';

			$withJoin = [
				'jfgs'=>explode(',','jfgs_name'),
				'fydy'=>explode(',','fydy_name'),
				'fylx'=>explode(',','fylx_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'fybz_id desc';

			$query = JffslbModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('jfgs_id');
			return json($data);
		}
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('jfgs_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('jfgs_id',explode(',',$list))){
			$data['jfgs_ids'] = $this->query("select jfgs_id,jfgs_name from cd_jfgs",'mysql');
		}
		return $data;
	}



}

