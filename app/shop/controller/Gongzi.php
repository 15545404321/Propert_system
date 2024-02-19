<?php 
/*
 module:		工资记录控制器
 create_time:	2023-01-18 14:31:51
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Gongzi as GongziModel;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Gongzi extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('gongzi_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = GongziModel::find($v);
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
			$where['gongzi_id'] = $this->request->post('gongzi_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');
			$where['a.xqgl_id'] = $this->request->post('xqgl_id', '', 'serach_in');
			$where['a.xz_ffdate'] = ['like',$this->request->post('xz_ffdate', '', 'serach_in')];
			$where['a.gz_zhouqi'] = ['like',$this->request->post('gz_zhouqi', '', 'serach_in')];
			$where['a.xzpici_id'] = $this->request->post('xzpici_id', '', 'serach_in');
			$where['a.gz_kqsh'] = $this->request->post('gz_kqsh', '', 'serach_in');
			$where['a.gz_kjsh'] = $this->request->post('gz_kjsh', '', 'serach_in');
			$where['a.gz_zjlsh'] = $this->request->post('gz_zjlsh', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'gongzi_id desc';

			$sql ="select 
a.*,
b.cname,
c.xqgl_name 
from cd_gongzi as a 
left join cd_shop_admin b on a.shop_admin_id = b.shop_admin_id 
left join cd_xqgl c on c.xqgl_id = a.xqgl_id 
where a.shop_id = ".session("shop.shop_id")."";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('xqgl_id,xzpici_id');
			return json($data);
		}
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'gongzi_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['gongzi_id']) throw new ValidateException ('参数错误');
		GongziModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,shop_admin_id,xz_ffdate,gz_zhouqi,gz_jine,gz_mingxi,gz_beizhu,xzpici_id,addtime';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gongzi::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['gz_zhouqi'] = implode(',',$data['gz_zhouqi']);
		$data['gz_mingxi'] = getItemData($data['gz_mingxi']);
		$data['addtime'] = !empty($data['addtime']) ? strtotime($data['addtime']) : '';

		try{
			$res = GongziModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Gongzi@afterShopAdd',array_merge($data,['gongzi_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'gongzi_id,shop_admin_id,xz_ffdate,gz_zhouqi,gz_jine,gz_mingxi,gz_beizhu,xzpici_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gongzi::class);

		$data['gz_zhouqi'] = implode(',',$data['gz_zhouqi']);
		$data['gz_mingxi'] = getItemData($data['gz_mingxi']);

		if($ret = hook('hook/Gongzi@beforShopUpdate',$data)){
			return $ret;
		}

		try{
			GongziModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Gongzi@afterShopUpdate',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('gongzi_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'gongzi_id,shop_admin_id,xz_ffdate,gz_zhouqi,gz_jine,gz_mingxi,gz_beizhu,xzpici_id';
		$res = GongziModel::field($field)->find($id);
		$res['gz_zhouqi'] = $res['gz_zhouqi'] ? explode(',',$res['gz_zhouqi']) : [];
		$res['gz_mingxi'] = json_decode($res['gz_mingxi'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('gongzi_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Gongzi@beforShopDelete',$idx)){
			return $ret;
		}

		GongziModel::destroy(['gongzi_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Gongzi@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('gongzi_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$withJoin = [
			'shopadmin'=>explode(',','cname'),
		];

		$field = 'gongzi_id,xz_ffdate,gz_zhouqi,gz_jine,gz_mingxi,xzpici_id,addtime,gz_kqsh,gz_kjsh,gz_zjlsh';
		$res = GongziModel::field($field)->withJoin($withJoin,'left')->find($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  考勤审核
 	*/
	public function batupkq(){
		$postField = 'gongzi_id,gz_kqsh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gongzi::class);

		$idx = explode(',',$data['gongzi_id']);
		unset($data['gongzi_id']);

		try{
			GongziModel::where(['gongzi_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  会计审核
 	*/
	public function batupkj(){
		$postField = 'gongzi_id,gz_kjsh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gongzi::class);

		$idx = explode(',',$data['gongzi_id']);
		unset($data['gongzi_id']);

		try{
			GongziModel::where(['gongzi_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  总经理审核
 	*/
	public function batupdate(){
		$postField = 'gongzi_id,gz_zjlsh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Gongzi::class);

		$idx = explode(',',$data['gongzi_id']);
		unset($data['gongzi_id']);

		try{
			GongziModel::where(['gongzi_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['gongzi_id'] = ['in',$this->request->param('gongzi_id', '', 'serach_in')];

		$where['gongzi.shop_id'] = session('shop.shop_id');
		$where['gongzi.xqgl_id'] = $this->request->param('xqgl_id', '', 'serach_in');
		$where['gongzi.xz_ffdate'] = ['like',$this->request->param('xz_ffdate', '', 'serach_in')];
		$where['gongzi.gz_zhouqi'] = ['like',$this->request->param('gz_zhouqi', '', 'serach_in')];
		$where['gongzi.xzpici_id'] = $this->request->param('xzpici_id', '', 'serach_in');
		$where['gongzi.gz_kqsh'] = $this->request->param('gz_kqsh', '', 'serach_in');
		$where['gongzi.gz_kjsh'] = $this->request->param('gz_kjsh', '', 'serach_in');
		$where['gongzi.gz_zjlsh'] = $this->request->param('gz_zjlsh', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'gongzi_id desc';

		$field = 'xz_ffdate,gz_zhouqi,gz_jine,gz_kjsh,gz_kqsh,addtime,xzpici_id,gz_beizhu,gz_mingxi,gz_zjlsh';

		$withJoin = [
			'shop'=>explode(',','shop_name'),
			'xqgl'=>explode(',','xqgl_name'),
			'shopadmin'=>explode(',','cname'),
		];

		$res = GongziModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$cache_key = 'Gongzi';
		if($page == 1){
			cache($cache_key,null);
			cache($cache_key,[]);
		}
		if($res['data']){
			cache($cache_key,array_merge(cache($cache_key),$res['data']));
			$data['percentage'] = ceil($page * 100/ceil($res['total']/$limit));
			$data['state'] =  'ok';
			return json($data);
		}
		if($state == 'ok'){
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$sheet->setCellValue('A1','结算月份');
			$sheet->setCellValue('B1','结算周期');
			$sheet->setCellValue('C1','发放金额');
			$sheet->setCellValue('D1','工资明细');
			$sheet->setCellValue('E1','薪资备注');
			$sheet->setCellValue('F1','发放批次');
			$sheet->setCellValue('G1','生成时间');
			$sheet->setCellValue('H1','考勤审核');
			$sheet->setCellValue('I1','会计审核');
			$sheet->setCellValue('J1','总经理审核');
			$sheet->setCellValue('K1','公司名称');
			$sheet->setCellValue('L1','项目名称');
			$sheet->setCellValue('M1','用户名称');

			foreach(cache($cache_key) as $k=>$v){
				$sheet->setCellValue('A'.($k+2),$v['xz_ffdate']);
				$sheet->setCellValue('B'.($k+2),$v['gz_zhouqi']);
				$sheet->setCellValue('C'.($k+2),$v['gz_jine']);
				$sheet->setCellValue('D'.($k+2),$v['gz_mingxi']);
				$sheet->setCellValue('E'.($k+2),$v['gz_beizhu']);
				$sheet->setCellValue('F'.($k+2),$v['xzpici_id']);
				$sheet->setCellValue('G'.($k+2),!empty($v['addtime']) ? date('Y-m-d H:i:s',$v['addtime']) : '');
				$sheet->setCellValue('H'.($k+2),getItemVal($v['gz_kqsh'],'[{"key":"已审","val":"1","label_color":"success"},{"key":"待审","val":"0","label_color":"warning"}]'));
				$sheet->setCellValue('I'.($k+2),getItemVal($v['gz_kjsh'],'[{"key":"已审","val":"1","label_color":"success"},{"key":"待审","val":"0","label_color":"warning"}]'));
				$sheet->setCellValue('J'.($k+2),getItemVal($v['gz_zjlsh'],'[{"key":"已审","val":"1","label_color":"success"},{"key":"待审","val":"0","label_color":"warning"}]'));
				$sheet->setCellValue('K'.($k+2),$v['shop']['shop_name']);
				$sheet->setCellValue('L'.($k+2),$v['xqgl']['xqgl_name']);
				$sheet->setCellValue('M'.($k+2),$v['shopadmin']['cname']);
			}

			$filename = '工资记录.'.config('my.dump_extension');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename);
 			header('Cache-Control: max-age=0');
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');exit;
		}
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('xqgl_id,xzpici_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('xqgl_id',explode(',',$list))){
			$data['xqgl_ids'] = $this->query("select xqgl_id,xqgl_name from cd_xqgl where shop_id = ".session("shop.shop_id")."",'mysql');
		}
		if(in_array('xzpici_id',explode(',',$list))){
			$data['xzpici_ids'] = $this->query("select xzpici_id,xzpici_id from cd_xzpici where shop_id=".session("shop.shop_id")."",'mysql');
		}
		return $data;
	}



}

