<?php 
/*
 module:		仪表管理控制器
 create_time:	2023-01-08 11:22:55
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\YiBiao as YiBiaoModel;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class YiBiao extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete','detail'])){
			$idx = $this->request->post('yibiao_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = YiBiaoModel::find($v);
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
			$where['yibiao_id'] = $this->request->post('yibiao_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.yblx_id'] = $this->request->post('yblx_id', '', 'serach_in');
			$where['a.ybzl_id'] = $this->request->post('ybzl_id', '', 'serach_in');
			$where['a.louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['a.fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
			$where['a.yibiao_status'] = $this->request->post('yibiao_status', '', 'serach_in');
			$where['a.yibiao_remarks'] = $this->request->post('yibiao_remarks', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'yibiao_id asc';

			$sql ="select a.*,b.yblx_name,c.ybzl_name,concat_ws('-',d.louyu_lyqz,d.louyu_name) as louyu_name,e.fcxx_fjbh from cd_yibiao as a 
left join cd_yblx as b on a.yblx_id = b.yblx_id
left join cd_ybzl as c on a.ybzl_id = c.ybzl_id
left join cd_louyu as d on a.louyu_id = d.louyu_id
left join cd_fcxx as e on a.fcxx_id = e.fcxx_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('yblx_id,ybzl_id,louyu_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_id(){
		$louyu_id =  $this->request->post('louyu_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fcxx_id,fcxx_fjbh from cd_fcxx where shop_id='.session("shop.shop_id").' and xqgl_id='.session("shop.xqgl_id").' and louyu_id ='.$louyu_id,'mysql');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'yibiao_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['yibiao_id']) throw new ValidateException ('参数错误');
		YiBiaoModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,yibiao_sn,yblx_id,ybzl_id,louyu_id,fcxx_id,yibiao_ybbl,yibiao_csds,yibiao_yblc,add_time,yibiao_status,yibiao_remarks';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\YiBiao::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['add_time'] = !empty($data['add_time']) ? strtotime($data['add_time']) : '';

        if($ret = hook('hook/YiBiao@beforShopAdd',$data)){
            return $ret;
        }

		try{
			$res = YiBiaoModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'yibiao_id,shop_id,xqgl_id,yibiao_sn,yblx_id,ybzl_id,louyu_id,fcxx_id,yibiao_ybbl,yibiao_csds,yibiao_yblc,add_time,yibiao_status,yibiao_remarks,cbgl_bqsl,yibiao_hbsj';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\YiBiao::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		if(!isset($data['ybzl_id'])){
			$data['ybzl_id'] = null;
		}

		if(!isset($data['louyu_id'])){
			$data['louyu_id'] = null;
		}
		$data['add_time'] = !empty($data['add_time']) ? strtotime($data['add_time']) : '';
		$data['yibiao_hbsj'] = !empty($data['yibiao_hbsj']) ? strtotime($data['yibiao_hbsj']) : '';

        if($ret = hook('hook/YiBiao@beforShopUpdate',$data)){
            return $ret;
        }

		try{
			YiBiaoModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

        if($ret = hook('hook/YiBiao@afterShopUpdate',$data)){
            return $ret;
        }

		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('yibiao_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fcxx_id,yibiao_id,shop_id,xqgl_id,yibiao_sn,yblx_id,ybzl_id,louyu_id,yibiao_ybbl,yibiao_csds,yibiao_yblc,add_time,yibiao_status,yibiao_remarks';
		$res = YiBiaoModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('yibiao_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		YiBiaoModel::destroy(['yibiao_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('yibiao_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'yibiao_id,yibiao_sn,yibiao_ybbl,yibiao_csds,yibiao_yblc,add_time,yibiao_status';
		$res = YiBiaoModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  导出
 	*/
	function dumpdata(){
		$page = $this->request->param('page', 1, 'intval');
		$limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

		$state = $this->request->param('state');
		$where = [];
		$where['yibiao_id'] = ['in',$this->request->param('yibiao_id', '', 'serach_in')];

		$where['yi_biao.shop_id'] = session('shop.shop_id');

		$where['yi_biao.xqgl_id'] = session('shop.xqgl_id');
		$where['yi_biao.yblx_id'] = $this->request->param('yblx_id', '', 'serach_in');
		$where['yi_biao.ybzl_id'] = $this->request->param('ybzl_id', '', 'serach_in');
		$where['yi_biao.louyu_id'] = $this->request->param('louyu_id', '', 'serach_in');
		$where['yi_biao.fcxx_id'] = $this->request->param('fcxx_id', '', 'serach_in');
		$where['yi_biao.yibiao_status'] = $this->request->param('yibiao_status', '', 'serach_in');
		$where['yi_biao.yibiao_remarks'] = $this->request->param('yibiao_remarks', '', 'serach_in');

		$order  = $this->request->param('order', '', 'serach_in');	//排序字段
		$sort  = $this->request->param('sort', '', 'serach_in');		//排序方式

		$orderby = ($sort && $order) ? $sort.' '.$order : 'yibiao_id desc';

		$field = 'yibiao_sn,yibiao_status,yibiao_ybbl,yibiao_csds,yibiao_yblc,add_time,yibiao_remarks';

		$withJoin = [
			'shop'=>explode(',','shop_name'),
			'xqgl'=>explode(',','xqgl_name'),
			'yblx'=>explode(',','yblx_name'),
			'ybzl'=>explode(',','ybzl_name'),
			'louyu'=>explode(',','louyu_name,louyu_lyqz'),
			'fcxx'=>explode(',','fcxx_fjbh'),
		];

		$res = YiBiaoModel::where(formatWhere($where))->field($field)->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

		$cache_key = 'YiBiao';
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

			$sheet->setCellValue('A1','仪表编号');
			$sheet->setCellValue('B1','房间号');
			$sheet->setCellValue('C1','仪表类型');
			$sheet->setCellValue('D1','仪表种类');
			$sheet->setCellValue('E1','仪表状态');
			$sheet->setCellValue('F1','上级仪表');
			$sheet->setCellValue('G1','计算损耗');
			$sheet->setCellValue('H1','分表用量修正');
			$sheet->setCellValue('I1','专摊用表');
			$sheet->setCellValue('J1','仪表倍率');
			$sheet->setCellValue('K1','仪表量程');
			$sheet->setCellValue('L1','安装时间');
			$sheet->setCellValue('M1','备注');

			foreach(cache($cache_key) as $k=>$v){

				$sheet->setCellValue('A'.($k+2),$v['yibiao_sn']);
				$sheet->setCellValue('B'.($k+2),$v['louyu']['louyu_lyqz'].'-'.$v['louyu']['louyu_name'].'-'.$v['fcxx']['fcxx_fjbh']);
				$sheet->setCellValue('C'.($k+2),$v['yblx']['yblx_name']);
				$sheet->setCellValue('D'.($k+2),$v['ybzl']['ybzl_name']);
				$sheet->setCellValue('E'.($k+2),getItemVal($v['yibiao_status'],'[{"key":"正常","val":"1","label_color":"primary"},{"key":"停用","val":"0","label_color":"danger"},{"key":"换表停用","val":"2","label_color":"warning"}]'));
				$sheet->setCellValue('F'.($k+2),'');
				$sheet->setCellValue('G'.($k+2),'');
				$sheet->setCellValue('H'.($k+2),'');
				$sheet->setCellValue('I'.($k+2),'');
				$sheet->setCellValue('J'.($k+2),$v['yibiao_ybbl']);
				$sheet->setCellValue('K'.($k+2),$v['yibiao_yblc']);
				$sheet->setCellValue('L'.($k+2),!empty($v['add_time']) ? date('Y-m-d',$v['add_time']) : '');
				$sheet->setCellValue('M'.($k+2),$v['yibiao_remarks']);
			}

			$filename = '仪表管理.'.config('my.dump_extension');
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
		return json(['status'=>200,'data'=>$this->getSqlField('yblx_id,ybzl_id,louyu_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('yblx_id',explode(',',$list))){
			$data['yblx_ids'] = $this->query("select yblx_id,yblx_name from cd_yblx",'mysql');
		}
		if(in_array('ybzl_id',explode(',',$list))){
			$data['ybzl_ids'] = _generateSelectTree($this->query("select ybzl_id,ybzl_name,ybzl_pid from cd_ybzl",'mysql'));
		}
		if(in_array('louyu_id',explode(',',$list))){
			$data['louyu_ids'] = _generateSelectTree($this->query("select louyu_id,louyu_name,louyu_pid from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")."",'mysql'));
		}
		return $data;
	}


/*start*/
	/*
 	* @Description  批量添加
 	*/
	public function batchAdd(){
		$data = $this->request->post($data);
		
		
        foreach ($data['data'] as $data_key => $data_item) {
            $data['data'][$data_key]['shop_id'] = session('shop.shop_id');
            $data['data'][$data_key]['xqgl_id'] = session('shop.xqgl_id');
            $data['data'][$data_key]['add_time'] = time();
            $data['data'][$data_key]['yibiao_csds'] = 0;
        }
		
		(new YiBiaoModel)->saveAll($data['data']);
		return json(['status'=>200,'msg'=>'添加成功']);
	}

    public function importData(){
        $data = $this->request->post();

        $list = [];
        $list_error1 = [];
        $list_error = [];
        foreach ($data as $key=>$val) {

            //    "房间号" => "A号楼-1单元-301"

            $fjh = explode('-',$val['房间号']);

            $yibiao = Db::name('yibiao')
                ->where('shop_id',session('shop.shop_id'))
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('yibiao_sn',trim($val['仪表编号']))->find();
            if ($yibiao) {
                $list_error1[] = $val;
                continue;
            }
            $lou = Db::name('louyu')
                ->where('shop_id',session('shop.shop_id'))
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('louyu_name',$fjh[0])->find();

            $danyuan = Db::name('louyu')
                ->where('shop_id',session('shop.shop_id'))
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('louyu_pid',$lou['louyu_id'])
                ->where('louyu_name',$fjh[1])->find();

            $fcxx = Db::name('fcxx')
                ->where('shop_id',session('shop.shop_id'))
                ->where('xqgl_id',session('shop.xqgl_id'))
                ->where('louyu_id',$danyuan['louyu_id'])
                ->where('fcxx_fjbh',$fjh[2])->find();

            if (empty($lou)||empty($danyuan)||empty($fcxx)) {
                $list_error[] = $val;
                continue;
            }

            $list[$key]['shop_id'] = session('shop.shop_id');
            $list[$key]['xqgl_id'] = session('shop.xqgl_id');
            $list[$key]['yibiao_sn'] = $val['仪表编号'];

            switch ($val['仪表类型']) {
                case '用户表': $list[$key]['yblx_id'] = 1; break;
                case '总表':  $list[$key]['yblx_id'] = 2; break;
                case '分摊表': $list[$key]['yblx_id'] = 3; break;
                case '物业表': $list[$key]['yblx_id'] = 4; break;
            }
            switch ($val['仪表种类']) {
                case '水表': $list[$key]['ybzl_id'] = 2; break;
                case '电表':  $list[$key]['ybzl_id'] = 3; break;
            }
            $list[$key]['yibiao_ybbl'] = $val['仪表倍率']??1;
            $list[$key]['yibiao_csds'] = 0;
            $list[$key]['yibiao_yblc'] = $val['仪表量程']??0;
            $list[$key]['add_time'] = strtotime($val['安装时间'])??time();
            $list[$key]['yibiao_remarks'] = $val['备注'];
            $list[$key]['yibiao_status'] = 1;
            $list[$key]['louyu_id'] = $danyuan['louyu_id'];
            $list[$key]['fcxx_id'] = $fcxx['fcxx_id'];

        }

        (new YiBiaoModel())->insertAll($list);

        if (empty($list_error)) {
            return json(['status'=>200]);
        }
        $cache_key = 'YiBiaoError';
        cache($cache_key,$list_error);
        return json(['status'=>200]);
    }

    function dumpDataError() {
        $cache_key = 'YiBiaoError';
//        dump($this->array_unique_fb(cache($cache_key)));
        $info = $this->array_unique_fb(cache($cache_key));
        if (!empty($info)) {
            cache($cache_key,null);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1','仪表编号');
            $sheet->setCellValue('B1','房间号');
            $sheet->setCellValue('C1','仪表类型');
            $sheet->setCellValue('D1','仪表种类');
            $sheet->setCellValue('E1','仪表状态');
            $sheet->setCellValue('F1','上级仪表');
            $sheet->setCellValue('G1','分表用量修正');
            $sheet->setCellValue('H1','专摊用表');
            $sheet->setCellValue('I1','仪表倍率');
            $sheet->setCellValue('J1','仪表量程');
            $sheet->setCellValue('K1','安装时间');
            $sheet->setCellValue('L1','备注');
            foreach($info as $k=>$v){
                $sheet->setCellValue('A'.($k+2),$v['仪表编号']);
                $sheet->setCellValue('B'.($k+2),$v['房间号']);
                $sheet->setCellValue('C'.($k+2),$v['仪表类型']);
                $sheet->setCellValue('D'.($k+2),$v['仪表种类']);
                $sheet->setCellValue('E'.($k+2),$v['仪表状态']);
                $sheet->setCellValue('F'.($k+2),$v['上级仪表']);
                $sheet->setCellValue('G'.($k+2),$v['分表用量修正']);
                $sheet->setCellValue('H'.($k+2),$v['专摊用表']);
                $sheet->setCellValue('I'.($k+2),$v['仪表倍率']);
                $sheet->setCellValue('J'.($k+2),$v['仪表量程']);
                $sheet->setCellValue('K'.($k+2),$v['安装时间']);
                $sheet->setCellValue('L'.($k+2),$v['备注']);
            }
            $filename = 'excel的错误信息.'.config('my.dump_extension');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }
        return view('index');
    }

    function array_unique_fb($array2D=[])
    {
        $temp = [];
        foreach ($array2D as $v) {
//            $v = join(",", $v);
            $v = json_encode($v,JSON_UNESCAPED_UNICODE);
            $temp[] = $v;
        }
        $temp = array_unique($temp);
        foreach ($temp as $k => $v) {
            $temp[$k] = json_decode($v,true);
        }
        return $temp;
    }
/*end*/



}

