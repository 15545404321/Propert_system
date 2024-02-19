<?php 
/*
 module:		交易汇总控制器
 create_time:	2023-02-17 15:46:15
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Jyhz as JyhzModel;
use think\facade\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Jyhz extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['detail','delete','cxsc'])){
			$idx = $this->request->post('yssj_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = JyhzModel::find($v);
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
			$where['yssj_id'] = $this->request->post('yssj_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');
			$where['a.xqgl_id'] = session('shop.xqgl_id');
			/*$where['a.fcxx_id'] = ['like',$this->request->post('fcxx_id', '', 'serach_in')];
			$where['a.yssj_fymc'] = $this->request->post('yssj_fymc', '', 'serach_in');
			$where['a.yssj_cwyf'] = $this->request->post('yssj_cwyf', '', 'serach_in');
			$where['a.fylx_id'] = $this->request->post('fylx_id', '', 'serach_in');
			$where['a.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['a.yssj_stuats'] = $this->request->post('yssj_stuats', '', 'serach_in');
			$where['a.member_id'] = $this->request->post('member_id', '', 'serach_in');
			$where['a.scys_id'] = $this->request->post('scys_id', '', 'serach_in');
			$where['a.sjlx_id'] = $this->request->post('sjlx_id', '', 'serach_in');
			$where['a.zjys_id'] = $this->request->post('zjys_id', '', 'serach_in');
			$where['a.lsys_id'] = $this->request->post('lsys_id', '', 'serach_in');
			$where['a.cbpc_id'] = $this->request->post('cbpc_id', '', 'serach_in');*/

            $where['a.yssj_fymc'] = $this->request->post('yssj_fymc', '', 'serach_in');

            $start_end = $this->request->post('start_end', '', 'serach_in');
            /*if (!empty($start_end)) {
                $where['a.yssj_kstime'] = ['>=',strtotime($start_end[0])];
                $where['a.yssj_jztime'] = ['<=',strtotime($start_end[1])+86400];
            }*/
            if (!empty($start_end)) {
                $where['a.yssj_fksj'] = ['>=',strtotime($start_end[0])];
                $where['a.yssj_fksj'] = ['<=',strtotime($start_end[1])+86400];
            }

            $skfs_id = $this->request->post('skfs_id', '', 'serach_in');
            if (!empty($skfs_id)) {
                $where['d.syt_method'] = $skfs_id;
                $sql ="select a.yssj_fymc,
sum(a.yssj_ysje) as yssj_ysje,
count(a.fcxx_id) as fcxx_id,
count(a.cewei_id) as cewei_id,
count(a.cbgl_id) as cbgl_id,
min(a.yssj_kstime) as yssj_kstime,
max(a.yssj_jztime) as yssj_jztime,
b.fylx_name,
c.fybz_name,
e.skfs_name
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id
left join cd_syt as d on a.syt_id = d.syt_id
left join cd_skfs as e on d.syt_method = e.skfs_id
group by yssj_fymc,syt_method";
            } else {
                $sql ="select a.yssj_fymc,
sum(a.yssj_ysje) as yssj_ysje,
count(a.fcxx_id) as fcxx_id,
count(a.cewei_id) as cewei_id,
count(a.cbgl_id) as cbgl_id,
min(a.yssj_kstime) as yssj_kstime,
max(a.yssj_jztime) as yssj_jztime,
b.fylx_name,
c.fybz_name
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id
where a.yssj_stuats > 0 
group by yssj_fymc";
            }

			$limit = ($page-1) * $limit.','.$limit;
            $order  = $this->request->post('order', '', 'serach_in');	//排序字段
            $sort  = $this->request->post('sort', '', 'serach_in');		//排序方式
            $orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id,lsys_id');

//            dump($res);exit;
			return json($data);
		}
	}


	/*
 	* @Description  撤销收款
 	*/
	function delete(){
		$idx =  $this->request->post('yssj_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Jyhz@beforShopDelete',$idx)){
			return $ret;
		}

		JyhzModel::destroy(['yssj_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Jyhz@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  重新生成
 	*/
	function cxsc(){
		$idx =  $this->request->post('yssj_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Jyhz@beforShopCxsc',$idx)){
			return $ret;
		}

		JyhzModel::destroy(['yssj_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Jyhz@afterShopCxsc',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id,lsys_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list) {
		$data = [];
		/*if(in_array('cbgl_id',explode(',',$list))){
			$data['cbgl_ids'] = $this->query("select cbgl_id,cbgl_id from cd_cbgl where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}*/
		if(in_array('fybz_id',explode(',',$list))){
			$data['fybz_ids'] = $this->query("select fybz_id,fybz_name from cd_fybz where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
        if(in_array('fybz_id',explode(',',$list))){
            $data['skfs_ids'] = $this->query("select skfs_id,skfs_name from cd_skfs",'mysql');
        }
		/*if(in_array('sjlx_id',explode(',',$list))){
			$data['sjlx_ids'] = $this->query("select sjlx_id,sjlx_name from cd_sjlx",'mysql');
		}
		if(in_array('zjys_id',explode(',',$list))){
			$data['zjys_ids'] = $this->query("select zjys_id,zjys_id from cd_zjys",'mysql');
		}
		if(in_array('lsys_id',explode(',',$list))){
			$data['lsys_ids'] = $this->query("select lsys_id as tval,lsys_id as tkey from cd_lsys where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}*/
		return $data;
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getFcxx_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select
a.fcxx_id as tval,
concat_ws('-',c.louyu_lyqz,c.louyu_name,a.fcxx_fjbh) as tkey 
from cd_fcxx as a
left join cd_louyu as c on a.louyu_id = c.louyu_id 
where a.xqgl_id=".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getCewei_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select 
a.cewei_id as tval,

concat_ws('-',g.tccd_name,f.cwqy_name,a.cewei_name) as tkey
from cd_cewei as a 

left join cd_tccd as g on g.tccd_id = a.tccd_id 
left join cd_cwqy as f on f.cwqy_id = a.cwqy_id

where a.xqgl_id=".session('shop.xqgl_id')."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getMember_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getScys_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select scys_id as tval,scys_id as tkey from cd_scys where xqgl_id = ".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

	/*
 	* @Description  获取下拉分页的数据
 	*/
	public function getCbpc_id(){
		$limit  = $this->request->post('limit', 20, 'intval');
		$page = $this->request->post('page', 1, 'intval');

		$where = [];
		$skip = ($page-1) * $limit.','.$limit;
		$data = $this->getSelectPageData("select cbpc_id as tval,cbpc_id as tkey from cd_cbpc where xqgl_id = ".session("shop.xqgl_id")."",$where,$skip); 
		return json(['status'=>200,'data'=>$data]);
	}

    /*
     * @Description  导出
     */
    function dumpdata(){
        $page = $this->request->param('page', 1, 'intval');
        $limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

        $state = $this->request->param('state');
        $where = [];
        $where['yssj_id'] = ['in',$this->request->param('yssj_id', '', 'serach_in')];

        $where['a.shop_id'] = session('shop.shop_id');
        $where['a.xqgl_id'] = session('shop.xqgl_id');
        $where['a.yssj_fymc'] = $this->request->param('yssj_fymc', '', 'serach_in');
        $start_end = $this->request->param('start_end', '', 'serach_in');
        /*if (!empty($start_end)) {
            $where['a.yssj_kstime'] = ['>=',strtotime($start_end[0])];
            $where['a.yssj_jztime'] = ['<=',strtotime($start_end[1])+86400];
        }*/
        if (!empty($start_end)) {
            $where['a.yssj_fksj'] = ['>=',strtotime($start_end[0])];
            $where['a.yssj_fksj'] = ['<=',strtotime($start_end[1])+86400];
        }
        $skfs_id = $this->request->param('skfs_id', '', 'serach_in');
        if (!empty($skfs_id)) {
            $where['d.syt_method'] = $skfs_id;
            $sql ="select a.yssj_fymc,
sum(a.yssj_ysje) as yssj_ysje,
count(a.fcxx_id) as fcxx_id,
count(a.cewei_id) as cewei_id,
count(a.cbgl_id) as cbgl_id,
min(a.yssj_kstime) as yssj_kstime,
max(a.yssj_jztime) as yssj_jztime,
b.fylx_name,
c.fybz_name,
e.skfs_name
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id
left join cd_syt as d on a.syt_id = d.syt_id
left join cd_skfs as e on d.syt_method = e.skfs_id
group by yssj_fymc,syt_method";
        } else {
            $sql ="select a.yssj_fymc,
sum(a.yssj_ysje) as yssj_ysje,
count(a.fcxx_id) as fcxx_id,
count(a.cewei_id) as cewei_id,
count(a.cbgl_id) as cbgl_id,
min(a.yssj_kstime) as yssj_kstime,
max(a.yssj_jztime) as yssj_jztime,
b.fylx_name,
c.fybz_name
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id
where a.yssj_stuats > 0 
group by yssj_fymc";
        }

        $limit = ($page-1) * $limit.','.$limit;

        $order  = $this->request->post('order', '', 'serach_in');	//排序字段
        $sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

        $orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

        $res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');
       
        $cache_key = 'Jyhz';

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

            $sheet->setCellValue('A1','费用名称');
            $sheet->setCellValue('B1','房产数');
            $sheet->setCellValue('C1','车产数');
            $sheet->setCellValue('D1','仪表数');
            $sheet->setCellValue('E1','收款方式');
            $sheet->setCellValue('F1','应收金额');
            $sheet->setCellValue('G1','开始日期');
            $sheet->setCellValue('H1','截至日期');

            foreach(cache($cache_key) as $k=>$v){
                $sheet->setCellValue('A'.($k+2),$v['yssj_fymc']); // 费用名称
                $sheet->setCellValue('B'.($k+2),$v['fcxx_id']); // 房产数
                $sheet->setCellValue('C'.($k+2),$v['cewei_id']); // 车产数
                $sheet->setCellValue('D'.($k+2),$v['cbgl_id']);
                $sheet->setCellValue('E'.($k+2),(isset($v['skfs_name']) && empty($v['skfs_name'])) ? $v['skfs_name'] : '');
                $sheet->setCellValue('F'.($k+2),$v['yssj_ysje']);
                $sheet->setCellValue('G'.($k+2),!empty($v['yssj_kstime']) ? date('Y-m-d',$v['yssj_kstime']) : '');
                $sheet->setCellValue('H'.($k+2),!empty($v['yssj_kstime']) ? date('Y-m-d',$v['yssj_kstime']) : '');
            }

            $filename = '交易汇总.'.config('my.dump_extension');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');exit;
        }
    }

}

