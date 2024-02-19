<?php 
/*
 module:		综合查询控制器
 create_time:	2023-02-18 17:22:29
 author:		
 contact:		
*/

namespace app\shop\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\exception\ValidateException;
use app\shop\model\Zhcx as ZhcxModel;
use think\facade\Db;

class Zhcx extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['detail','delete','cxsc'])){
			$idx = $this->request->post('yssj_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = ZhcxModel::find($v);
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
            $where['a.fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
            $where['a.cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
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
			$where['a.cbpc_id'] = $this->request->post('cbpc_id', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

			$sql ="select 
a.*,
b.fylx_name,
c.fybz_name,
concat_ws('-',z.louyu_lyqz,z.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,
e.member_name,
concat_ws('-',x.tccd_name,w.cwqy_name,f.cewei_name) as cewei_name,
d.fcxx_jzmj  
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_member as e on a.member_id = e.member_id 
left join cd_louyu as z on d.louyu_id = z.louyu_id 
left join cd_cewei as f on a.cewei_id = f.cewei_id 
left join cd_tccd as x on x.tccd_id = f.tccd_id 
left join cd_cwqy as w on w.cwqy_id = f.cwqy_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('cbgl_id,fylx_id,fybz_id,sjlx_id,zjys_id,lsys_id');
			return json($data);
		}
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
	private function getSqlField($list){
		$data = [];
		if(in_array('cbgl_id',explode(',',$list))){
			$data['cbgl_ids'] = $this->query("select cbgl_id,cbgl_id from cd_cbgl where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('fylx_id',explode(',',$list))){
			$data['fylx_ids'] = $this->query("select fylx_id,fylx_name from cd_fylx",'mysql');
		}
		if(in_array('fybz_id',explode(',',$list))){
			$data['fybz_ids'] = $this->query("select fybz_id,fybz_name from cd_fybz where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
		if(in_array('sjlx_id',explode(',',$list))){
			$data['sjlx_ids'] = $this->query("select sjlx_id,sjlx_name from cd_sjlx",'mysql');
		}
		if(in_array('zjys_id',explode(',',$list))){
			$data['zjys_ids'] = $this->query("select zjys_id,zjys_id from cd_zjys",'mysql');
		}
		if(in_array('lsys_id',explode(',',$list))){
			$data['lsys_ids'] = $this->query("select lsys_id as tval,lsys_id as tkey from cd_lsys where xqgl_id=".session("shop.xqgl_id")."",'mysql');
		}
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
    function dumpdata() {
        $page = $this->request->param('page', 1, 'intval');
        $limit = config('my.dumpsize') ? config('my.dumpsize') : 1000;

        $state = $this->request->param('state');

        $where = [];
        $where['yssj_id'] = $this->request->post('yssj_id', '', 'serach_in');

        $where['a.shop_id'] = session('shop.shop_id');

        $where['a.xqgl_id'] = session('shop.xqgl_id');
        $where['a.fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');
        $where['a.cewei_id'] = $this->request->post('cewei_id', '', 'serach_in');
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
        $where['a.cbpc_id'] = $this->request->post('cbpc_id', '', 'serach_in');

        $order  = $this->request->post('order', '', 'serach_in');	//排序字段
        $sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

        $orderby = ($sort && $order) ? $sort.' '.$order : 'yssj_id desc';

        $sql ="select 
a.*,
b.fylx_name,
c.fybz_name,
concat_ws('-',z.louyu_lyqz,z.louyu_name,d.fcxx_fjbh) as fcxx_fjbh,
e.member_name,
concat_ws('-',x.tccd_name,w.cwqy_name,f.cewei_name) as cewei_name,
d.fcxx_jzmj  
from cd_yssj as a 
left join cd_fylx as b on a.fylx_id = b.fylx_id 
left join cd_fybz as c on a.fybz_id = c.fybz_id 
left join cd_fcxx as d on a.fcxx_id = d.fcxx_id 
left join cd_member as e on a.member_id = e.member_id 
left join cd_louyu as z on d.louyu_id = z.louyu_id 
left join cd_cewei as f on a.cewei_id = f.cewei_id 
left join cd_tccd as x on x.tccd_id = f.tccd_id 
left join cd_cwqy as w on w.cwqy_id = f.cwqy_id";
        $limit = ($page-1) * $limit.','.$limit;

        $res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

        $cache_key = 'Zhcx';

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

            $sheet->setCellValue('A1','房屋资产');
            $sheet->setCellValue('B1','车位资产');
            $sheet->setCellValue('C1','建筑面积');
            $sheet->setCellValue('D1','费用名称');
            $sheet->setCellValue('E1','财务月份');
            $sheet->setCellValue('F1','开始日期');
            $sheet->setCellValue('G1','截至日期');
            $sheet->setCellValue('H1','费用单价');

            $sheet->setCellValue('I1','应收金额');
            $sheet->setCellValue('J1','费用类型');
            $sheet->setCellValue('K1','费用标准');
            $sheet->setCellValue('L1','付款状态');
            $sheet->setCellValue('M1','客户名称');

            foreach(cache($cache_key) as $k=>$v){
                $sheet->setCellValue('A'.($k+2),$v['fcxx_fjbh']); // 房屋资产
                $sheet->setCellValue('B'.($k+2),$v['cewei_name']); // 车位资产
                $sheet->setCellValue('C'.($k+2),$v['fcxx_jzmj']); // 建筑面积
                $sheet->setCellValue('D'.($k+2),$v['yssj_fymc']); // 费用名称
                $sheet->setCellValue('E'.($k+2),$v['yssj_cwyf']); // 财务月份
                $sheet->setCellValue('F'.($k+2),!empty($v['yssj_kstime']) ? date('Y-m-d',$v['yssj_kstime']) : ''); // 开始日期
                $sheet->setCellValue('G'.($k+2),!empty($v['yssj_jztime']) ? date('Y-m-d',$v['yssj_jztime']) : ''); // 截至日期
                $sheet->setCellValue('H'.($k+2),$v['yssj_fydj']); // 费用单价
                $sheet->setCellValue('I'.($k+2),$v['yssj_ysje']); // 应收金额
                $sheet->setCellValue('J'.($k+2),$v['fylx_name']); // 费用类型
                $sheet->setCellValue('K'.($k+2),$v['fybz_name']); // 费用标准
                $sheet->setCellValue('L'.($k+2),getItemVal($v['yssj_stuats'],'[{"key":"已付款","val":"1","label_color":"success"},{"key":"未付款","val":"0","label_color":"danger"},{"key":"已退款","val":"2","label_color":"info"},{"key":"转预存","val":"3","label_color":"primary"}]')); // 付款状态
                $sheet->setCellValue('M'.($k+2),$v['member_name']); // 客户名称
            }

            $filename = '宗合查询.'.config('my.dump_extension');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');exit;
        }
    }

    /*start*/
    /*
    * @Description  获取远程搜索字段信息
    */
    public function remoteMemberidList(){
        $queryString = $this->request->post('queryString');
        $dataval = $this->request->post('dataval');
        if($queryString){
            $sqlstr = "member_name like '".$queryString."%'";
        }
        if($dataval){
            $sqlstr = 'member_id = '.$dataval;
        }
//		$data = $this->query('select member_id,member_name from cd_member where shop_id=".session("shop.shop_id")." where '.$sqlstr,'mysql');
        $data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));

        return json(['status'=>200,'data'=>$data]);
    }
    /*end*/

}

