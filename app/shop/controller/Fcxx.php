<?php 
/*
 module:		房产信息控制器
 create_time:	2023-01-27 08:18:21
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Fcxx as FcxxModel;
use think\facade\Db;

class Fcxx extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['detail','update','getUpdateInfo','delete','zcgl','getZcglInfo'])){
			$idx = $this->request->post('fcxx_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = FcxxModel::find($v);
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
			$where['fcxx_id'] = $this->request->post('fcxx_id', '', 'serach_in');

			$where['a.shop_id'] = session('shop.shop_id');

			$where['a.xqgl_id'] = session('shop.xqgl_id');
			$where['a.louyu_id'] = ['like',$this->request->post('louyu_id', '', 'serach_in')];
			$where['a.fcxx_fjbh'] = ['like',$this->request->post('fcxx_fjbh', '', 'serach_in')];
			$where['a.fwlx_id'] = $this->request->post('fwlx_id', '', 'serach_in');
			$where['a.member_id'] = ['like',$this->request->post('member_id', '', 'serach_in')];
			$where['a.fcxx_ghjl'] = $this->request->post('fcxx_ghjl', '', 'serach_in');

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'louyu_id asc,fcxx_lcpx asc,fcxx_id asc';

			$sql ="select a.*,concat_ws('_',b.louyu_lyqz,b.louyu_name) as louyu_name,c.fwlx_name
from cd_fcxx a
left join cd_louyu b on a.louyu_id=b.louyu_id
left join cd_fwlx c on a.fwlx_id=c.fwlx_id";
			$limit = ($page-1) * $limit.','.$limit;

			$res = loadList($sql,formatWhere($where),$limit,$orderby,'mysql');

			foreach($res['data'] as $k=>$v){
				if($v['member_id']){
					$res['data'][$k]['member_id'] = Db::query("select member_name from  cd_member where shop_id=".session("shop.shop_id")." and xqgl_id=".session("shop.xqgl_id")." and member_id=".$v['member_id']."")[0]['member_name'];
				}
			}

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('louyu_id,fwlx_id');
			return json($data);
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
		$data = _generateSelectTree($this->query('select member_id as tval,concat_ws("_",member_name,member_tel) as tkey from cd_member where xqgl_id='.session("shop.xqgl_id").' and '.$sqlstr,'mysql'));
		return json(['status'=>200,'data'=>$data]);
	}

/*end*/
	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'fcxx_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['fcxx_id']) throw new ValidateException ('参数错误');
		FcxxModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*start*/
	/*
 	* @Description  添加房屋
 	*/
	public function add(){
		$postField = 'shop_id,xqgl_id,louyu_id,fcxx_szlc,fcxx_fjbh,fcxx_jzmj,fcxx_tnmj,fwlx_id,member_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fcxx::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

        $data['fcxx_lcpx'] = $data['fcxx_szlc'];
		if (stripos($data['fcxx_szlc'],'负')) {
            $data['fcxx_lcpx'] = 1;
        }

		try{
			$res = FcxxModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}

	/*
 	* @Description  查看详情
 	*/
	function detail(){
	    // 例如：点击A楼一单元101查看详情，能看到101这户所有的信息（家里几口人，这个小区几个房子，有没有门市，车牌号多少，车库，车位信息）
		$id =  $this->request->post('fcxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');

		$re = FcxxModel::findOrEmpty($id);

        $res = [];
        $member_id = $re->member_id;

        if (!empty($member_id)) {
            $member = Db::name('member')->where('member_id',$member_id)->find();

            $member_idx_arr = explode(',',$member['member_idx']);
            $fcxx_idx_arr = explode(',',$member['fcxx_idx']);

            $member_idx = Db::name('member')->where('member_id','in',$member_idx_arr)->column('member_name');

            $cewei_namex = Db::name('cewei')->alias('c')
                ->field('c.cewei_name,t.tccd_name,cw.cwqy_name')
                ->leftJoin('tccd t','t.tccd_id=c.tccd_id')
                ->leftJoin('cwqy cw','cw.cwqy_id=c.tccd_id')
                ->where('c.member_id',$member_id)->select();

            $cewei_namex_1 = [];
            foreach ($cewei_namex as $cewei_namex_item) {
                $cewei_namex_1[] = $cewei_namex_item['tccd_name'].'-'.$cewei_namex_item['cwqy_name'].'-'.$cewei_namex_item['cewei_name'];
            }

            $car_namex = Db::name('car')->where('member_id',$member_id)->column('car_name');

            // 房产
            $fcxx_fjbhx = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,d.louyu_name as danyaun_name,l.louyu_name')
                ->leftJoin('louyu d','f.louyu_id=d.louyu_id')
                ->leftJoin('louyu l','d.louyu_pid=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)->select();

            $fcxx_fjbhx_1 = [];
            foreach ($fcxx_fjbhx as $fcxx_fjbhx_item) {
                $fcxx_fjbhx_1[] = $fcxx_fjbhx_item['louyu_name'].'-'.$fcxx_fjbhx_item['danyaun_name'].'-'.$fcxx_fjbhx_item['fcxx_fjbh'];
            }

            // 门市
            $fcxx_ms = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,l.louyu_name as danyaun_name,l.louyu_name')
//                ->leftJoin('louyu d','f.louyu_id=d.louyu_id')
                ->leftJoin('louyu l','f.louyu_id=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)
                ->where('f.fwlx_id',2)
                ->whereNull('l.louyu_pid')->select();

            $fcxx_fjbhx_2 = [];
            foreach ($fcxx_ms as $fcxx_ms_item) {
                $fcxx_fjbhx_2[] = $fcxx_ms_item['louyu_name'].'-'.$fcxx_ms_item['danyaun_name'].'-'.$fcxx_ms_item['fcxx_fjbh'];
            }

            //车库
            $fcxx_ck = Db::name('fcxx')->alias('f')
                ->field('f.fcxx_fjbh,l.louyu_name as danyaun_name,l.louyu_name')
                ->leftJoin('louyu l','f.louyu_id=l.louyu_id')
                ->where('f.fcxx_id','in',$fcxx_idx_arr)
                ->where('f.fwlx_id',4)
                ->whereNull('l.louyu_pid')->select();

            $fcxx_fjbhx_3 = [];
            foreach ($fcxx_ck as $fcxx_ck_item) {
                $fcxx_fjbhx_3[] = $fcxx_ck_item['louyu_name'].'-'.$fcxx_ck_item['danyaun_name'].'-'.$fcxx_ck_item['fcxx_fjbh'];
            }

            $res['member_name'] = $member['member_name'];
            $res['member_idx']  = implode(',',$member_idx);
            $res['fcxx_fjbhx']  = implode(',',$fcxx_fjbhx_1);
            $res['cewei_namex'] = implode(',',$cewei_namex_1);
            $res['fcxx_ms']     = implode(',',$fcxx_fjbhx_2);
            $res['fcxx_ck']     = implode(',',$fcxx_fjbhx_3);
            $res['car_namex']   = implode(',',$car_namex);

        }

        // 本房产
        $fcxx_fjbhx_0 = Db::name('fcxx')->alias('f')
            ->field('f.fcxx_fjbh,d.louyu_name as danyaun_name,l.louyu_name')
            ->leftJoin('louyu d','f.louyu_id=d.louyu_id')
            ->leftJoin('louyu l','d.louyu_pid=l.louyu_id')
            ->where('f.fcxx_id',$re->fcxx_id)->select();

        $res['fcxx_fjbh'] = $fcxx_fjbhx_0[0]['louyu_name'].'-'.$fcxx_fjbhx_0[0]['danyaun_name'].'-'.$fcxx_fjbhx_0[0]['fcxx_fjbh'];

		if(empty($res)){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}
    /*end*/


	/*
 	* @Description  修改房屋
 	*/
	public function update(){
		$postField = 'fcxx_id,fcxx_jzmj,fcxx_tnmj,fwlx_id,fcxx_fjbh';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fcxx::class);

		try{
			FcxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('fcxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fcxx_id,fcxx_fjbh,fcxx_jzmj,fcxx_tnmj,fwlx_id';
		$res = FcxxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除房屋
 	*/
	function delete(){
		$idx =  $this->request->post('fcxx_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Fcxx@beforShopDelete',$idx)){
            return $ret;
        }

		FcxxModel::destroy(['fcxx_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  资产关联
 	*/
	public function zcgl(){
		$postField = 'fcxx_id,member_id';
		$data = $this->request->only(explode(',',$postField),'post',null);
		$this->validate($data,\app\shop\validate\Fcxx::class);

        if($ret = hook('hook/Fcxx@beforShopZcgl',$data)){
            return $ret;
        }

		try{
			FcxxModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getZcglInfo(){
		$id =  $this->request->post('fcxx_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'fcxx_id,member_id';
		$res = FcxxModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  修改面积
 	*/
	public function batupdate(){
		$postField = 'fcxx_id,fcxx_jzmj,fcxx_tnmj';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fcxx::class);

		$idx = explode(',',$data['fcxx_id']);
		unset($data['fcxx_id']);

		try{
			FcxxModel::where(['fcxx_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}


	/*
 	* @Description  房屋类型
 	*/
	public function fwlxupdate(){
		$postField = 'fcxx_id,fwlx_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Fcxx::class);

		$idx = explode(',',$data['fcxx_id']);
		unset($data['fcxx_id']);

		try{
			FcxxModel::where(['fcxx_id'=>$idx])->update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
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



}

