<?php 
/*
 module:		资产台账控制器
 create_time:	2023-01-06 16:02:34
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Zcml as ZcmlModel;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Zcml extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('zcml_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = ZcmlModel::find($v);
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
			$where['zcml_id'] = $this->request->post('zcml_id', '', 'serach_in');

			$where['shop_id'] = session('shop.shop_id');

			$where['xqgl_id'] = session('shop.xqgl_id');
			$where['zcml_name'] = $this->request->post('zcml_name', '', 'serach_in');
			$where['zcml_bm'] = $this->request->post('zcml_bm', '', 'serach_in');
			$where['zcml_type'] = $this->request->post('zcml_type', '', 'serach_in');
			$where['zcml_neirong'] = $this->request->post('zcml_neirong', '', 'serach_in');
			$where['zclb_fid'] = $this->request->post('zclb_fid', '', 'serach_in');
			$where['zclb_id'] = $this->request->post('zclb_id', '', 'serach_in');

			$field = 'zcml_id,zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zclb_fid,zclb_id';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'zcml_id desc';

			$query = ZcmlModel::field($field);

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			foreach($res['data'] as $k=>$v){
				if($v['xqgl_id']){
					$res['data'][$k]['xqgl_id'] = Db::query("select  from  where =".$v['xqgl_id']."")[0][''];
				}
				if($v['zclb_fid']){
					$res['data'][$k]['zclb_fid'] = Db::query("select zclb_name from  cd_zclb where zclb_fid = 0 and zclb_id=".$v['zclb_fid']."")[0]['zclb_name'];
				}
				if($v['zclb_id']){
					$res['data'][$k]['zclb_id'] = Db::query("select zclb_name from  cd_zclb where zclb_id=".$v['zclb_id']."")[0]['zclb_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('zclb_fid');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getZclb_id(){
		$zclb_fid =  $this->request->post('zclb_fid', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select zclb_id,zclb_name from cd_zclb where zclb_fid ='.$zclb_fid,'mysql');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'zcml_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['zcml_id']) throw new ValidateException ('参数错误');
		ZcmlModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zcml_neirong,zclb_fid,zclb_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zcml::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['zcml_time'] = !empty($data['zcml_time']) ? strtotime($data['zcml_time']) : '';
		$data['zcml_pic'] = getItemData($data['zcml_pic']);
		$data['zcml_fj'] = getItemData($data['zcml_fj']);

		try{
			$res = ZcmlModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'zcml_id,shop_id,xqgl_id,zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zcml_neirong,zclb_fid,zclb_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Zcml::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['zcml_time'] = !empty($data['zcml_time']) ? strtotime($data['zcml_time']) : '';
		$data['zcml_pic'] = getItemData($data['zcml_pic']);
		$data['zcml_fj'] = getItemData($data['zcml_fj']);

		try{
			ZcmlModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('zcml_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zcml_id,shop_id,xqgl_id,zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zcml_neirong,zclb_fid,zclb_id';
		$res = ZcmlModel::field($field)->find($id);
		$res['zcml_pic'] = json_decode($res['zcml_pic'],true);
		$res['zcml_fj'] = json_decode($res['zcml_fj'],true);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('zcml_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ZcmlModel::destroy(['zcml_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['zcml_id'] = ['in',$this->request->param('zcml_id', '', 'serach_in')];

		$where['zcml.shop_id'] = session('shop.shop_id');

		$where['zcml.xqgl_id'] = session('shop.xqgl_id');
		$where['zcml.zcml_name'] = $this->request->param('zcml_name', '', 'serach_in');
		$where['zcml.zcml_bm'] = $this->request->param('zcml_bm', '', 'serach_in');
		$where['zcml.zcml_type'] = $this->request->param('zcml_type', '', 'serach_in');
		$where['zcml.zcml_neirong'] = $this->request->param('zcml_neirong', '', 'serach_in');
		$where['zcml.zclb_fid'] = $this->request->param('zclb_fid', '', 'serach_in');
		$where['zcml.zclb_id'] = $this->request->param('zclb_id', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'zcml_id desc';

		$field = 'zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zcml_neirong';

		$withJoin = [
			'zclb'=>explode(',','zclb_name,zclb_id'),
			'zclb'=>explode(',','zclb_name,zclb_id'),
		];

		$res = ZcmlModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$cache_key = 'Zcml';
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

			$sheet->setCellValue('A1','资产名称');
			$sheet->setCellValue('B1','资产编码');
			$sheet->setCellValue('C1','资产性质');
			$sheet->setCellValue('D1','添加时间');
			$sheet->setCellValue('E1','资产照片');
			$sheet->setCellValue('F1','资产附件');
			$sheet->setCellValue('G1','资产介绍');
			$sheet->setCellValue('H1','编号');
			$sheet->setCellValue('I1','类别名称');

			foreach(cache($cache_key) as $k=>$v){
				$sheet->setCellValue('A'.($k+2),$v['zcml_name']);
				$sheet->setCellValue('B'.($k+2),$v['zcml_bm']);
				$sheet->setCellValue('C'.($k+2),getItemVal($v['zcml_type'],'[{"key":"低值易耗","val":"1","label_color":"info"},{"key":"固定资产","val":"2","label_color":"success"}]'));
				$sheet->setCellValue('D'.($k+2),!empty($v['zcml_time']) ? date('Y-m-d H:i:s',$v['zcml_time']) : '');
				$sheet->setCellValue('E'.($k+2),$v['zcml_pic']);
				$sheet->setCellValue('F'.($k+2),$v['zcml_fj']);
				$sheet->setCellValue('G'.($k+2),$v['zcml_neirong']);
				$sheet->setCellValue('H'.($k+2),$v['zclb']['zclb_id']);
				$sheet->setCellValue('I'.($k+2),$v['zclb']['zclb_name']);
			}

			$filename = '资产台账.'.config('my.dump_extension');
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename);
 			header('Cache-Control: max-age=0');
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');exit;
		}
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('zcml_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'zcml_id,zcml_name,zcml_bm,zcml_type,zcml_time,zcml_pic,zcml_fj,zclb_fid,zclb_id';
		$res = ZcmlModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('zclb_fid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('zclb_fid',explode(',',$list))){
			$data['zclb_fids'] = $this->query("select zclb_id,zclb_name from cd_zclb where zclb_fid = 0",'mysql');
		}
		return $data;
	}



}

