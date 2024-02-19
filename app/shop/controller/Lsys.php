<?php 
/*
 module:		临时应收控制器
 create_time:	2023-01-10 12:45:34
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Lsys as LsysModel;
use think\facade\Db;

class Lsys extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete'])){
			$idx = $this->request->post('lsys_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = LsysModel::find($v);
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
			$where['lsys_id'] = $this->request->post('lsys_id', '', 'serach_in');
			$where['lsys.jflx_id'] = $this->request->post('jflx_id', '', 'serach_in');
			$where['lsys.fydy_id'] = $this->request->post('fydy_id', '', 'serach_in');
			$where['lsys.fybz_id'] = $this->request->post('fybz_id', '', 'serach_in');
			$where['lsys.lsys_ysje'] = $this->request->post('lsys_ysje', '', 'serach_in');
			$where['lsys.lsys_bz'] = $this->request->post('lsys_bz', '', 'serach_in');

			$where['lsys.shop_id'] = session('shop.shop_id');

			$where['lsys.xqgl_id'] = session('shop.xqgl_id');

			$field = 'lsys_id,lsys_kstime,lsys_jstime,fybz_id,lsys_ysje,lsys_bz,shop_id,xqgl_id,fcxx_idx';

			$withJoin = [
				'jflx'=>explode(',','jflx_name'),
				'fybz'=>explode(',','fybz_name'),
				'fydy'=>explode(',','fydy_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'lsys_id desc';

			$query = LsysModel::field($field);

            $re = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();
            $res = $re;
			foreach ($re['data'] as $re_key => $re_item) {

                $fcxx_idx = explode(',',$re_item['fcxx_idx']);

                if (count($fcxx_idx) == 1) {

                    $fcxx = Db::name('fcxx')
                        ->where('fcxx_id','in',$fcxx_idx)->find();

                    $danyuan = Db::name('louyu')
                        ->whereNotNull('louyu_pid')->where('louyu_id',$fcxx['louyu_id'])->find();

                    if (empty($danyuan)) {

                        $louyu_name = Db::name('louyu')
                            ->whereNull('louyu_pid')->where('louyu_id',$fcxx['louyu_id'])->value('louyu_name');

                        $res['data']['louyu_name'] = $louyu_name.'-'.$louyu_name.'-'.$fcxx['fcxx_fjbh'];

                    } else {

                        $louyu_name = Db::name('louyu')
                            ->whereNull('louyu_pid')->where('louyu_id',$danyuan['louyu_pid'])->value('louyu_name');

                        $res['data'][$re_key]['louyu_name'] = $louyu_name.'-'.$danyuan['louyu_name'].'-'.$fcxx['fcxx_fjbh'];

                    }

                } else {

                    $fcxx_louyu_id = Db::name('fcxx')
                        ->where('fcxx_id','in',$fcxx_idx)->column('louyu_id');

                    $danyuan = Db::name('louyu')
                        ->whereNotNull('louyu_pid')->where('louyu_id','in',$fcxx_louyu_id)->column('louyu_pid');

                    $louyu = Db::name('louyu')
                        ->whereNull('louyu_pid')->where('louyu_id','in',$fcxx_louyu_id)->column('louyu_id');

                    $in_louyu_id = array_unique(array_merge($danyuan,$louyu));

                    $louyu_name = Db::name('louyu')
                        ->whereNull('louyu_pid')->where('louyu_id','in',$in_louyu_id)->column('louyu_name');

                    $res['data'][$re_key]['louyu_name'] = implode(',',$louyu_name);
                }

            }

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('jflx_id,fydy_id');
			return json($data);
		}
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFcxx_idx(){
		$jflx_id =  $this->request->post('jflx_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query(' where jflx_id ='.$jflx_id,'mysql');
		return json($data);
	}


	/*
	* @Description  获取定义sql语句的字段信息
	*/
	public function getFybz_id(){
		$fydy_id =  $this->request->post('fydy_id', '', 'serach_in');
		$data['status'] = 200;
		$data['data'] = $this->query('select fybz_id,fybz_name from cd_fybz where fydy_id ='.$fydy_id,'mysql');
		return json($data);
	}


	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'lsys_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['lsys_id']) throw new ValidateException ('参数错误');
		LsysModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}




	/*
 	* @Description  权限节点
 	*/
	function getRoleAccess(){
		$nodes = (new \utils\AuthAccess())->getNodeMenus(298,0);

		if($baseNode = hook('hook/Base@getShopNode',$nodes)){
			$nodes = array_merge($baseNode,$nodes);
		}

		array_multisort(array_column($nodes, 'sortid'),SORT_ASC,$nodes );
		return json(['status'=>200,'menus'=>$nodes]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('lsys_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

        if($ret = hook('hook/Lsys@beforShopDelete',$idx)){
            return $ret;
        }

		LsysModel::destroy(['lsys_id'=>explode(',',$idx)],true);

        if($ret = hook('hook/Lsys@afterShopDelete',$idx)){
            return $ret;
        }

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('jflx_id,fydy_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('jflx_id',explode(',',$list))){
			$data['jflx_ids'] = $this->query("select jflx_id,jflx_name from cd_jflx",'mysql');
		}
		if(in_array('fydy_id',explode(',',$list))){
			$data['fydy_ids'] = $this->query("select fydy_id,fydy_name from cd_fydy where fylx_id > 2 and xqgl_id = ".session("shop.xqgl_id")."",'mysql');
		}
		return $data;
	}

	/*start*/
	/*
 	* @Description  临时应收
 	*/
	public function add(){
		$postField = 'lsys_kstime,lsys_jstime,jflx_id,fydy_id,fybz_id,fcxx_idx,lsys_ysje,lsys_bz,shop_id,xqgl_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\shop\validate\Lsys::class);

		$data['lsys_kstime'] = !empty($data['lsys_kstime']) ? strtotime($data['lsys_kstime']) : '';
		$data['lsys_jstime'] = !empty($data['lsys_jstime']) ? strtotime($data['lsys_jstime']) : '';
		$data['fcxx_idx'] = implode(',',array_filter($data['fcxx_idx']));
		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');

		try{
			$res = LsysModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Lsys@afterShopAdd',array_merge($data,['lsys_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}

    /*
    * @Description  重新生成
    */
    public function update(){
        $postField = 'lsys_id,lsys_kstime,lsys_jstime,jflx_id,fydy_id,fybz_id,fcxx_idx,lsys_ysje,lsys_bz,shop_id,xqgl_id';
        $data = $this->request->only(explode(',',$postField),'post',null);

        $this->validate($data,\app\shop\validate\Lsys::class);

        $data['lsys_kstime'] = !empty($data['lsys_kstime']) ? strtotime($data['lsys_kstime']) : '';
        $data['lsys_jstime'] = !empty($data['lsys_jstime']) ? strtotime($data['lsys_jstime']) : '';
        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');

        $fcxx_idx = [];
        foreach ($data['fcxx_idx'] as $data_fcxx_idx) {
            if (is_numeric($data_fcxx_idx)) {
                $fcxx_idx[] = $data_fcxx_idx;
            }
        }
        $data['fcxx_idx'] = implode(',',$fcxx_idx);

        if($ret = hook('hook/Lsys@beforShopUpdate',$data)){
            return $ret;
        }

        try{
            LsysModel::update($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }

        if($ret = hook('hook/Lsys@afterShopUpdate',$data)){
            return $ret;
        }

        return json(['status'=>200,'msg'=>'修改成功']);
    }

	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('lsys_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'lsys_id,lsys_kstime,lsys_jstime,jflx_id,fydy_id,fybz_id,lsys_ysje,lsys_bz,shop_id,xqgl_id,fcxx_idx';
		$res = LsysModel::field($field)->find($id);

        $fcxx_idx = explode(',',$res['fcxx_idx']);

        $danyuan = Db::name('fcxx')->alias('a')
            ->leftJoin('louyu b','a.louyu_id = b.louyu_id')
            ->whereNotNull('b.louyu_pid')
            ->wherein('a.fcxx_id',$fcxx_idx)->column('b.louyu_id');

        $louyu= Db::name('louyu')->wherein('louyu_id',$danyuan)->column('louyu_pid');

        $louyu_C = $louyu;
        $louyu_L = $louyu;
        $danyuan_D = $danyuan;
        foreach ($louyu as $louyu_key => $louyu_item) {
            $louyu_C[$louyu_key] = 'C'.$louyu_item;
            $louyu_L[$louyu_key] = 'L'.$louyu_item;
        }

        foreach ($danyuan as $danyuan_key => $danyuan_item) {
            $danyuan_D[$danyuan_key] = 'D'.$danyuan_item;
        }

        $louyu_All = array_merge($louyu_L,$louyu_C);

        $fcxx_idx = array_merge(array_unique(array_merge($louyu_All,$danyuan_D)),$fcxx_idx);
		$res['fcxx_idx'] = $fcxx_idx;
//		dump($res);exit;
		return json(['status'=>200,'data'=>$res]);
	}

	/*
 	* @Description  权限节点
 	*/
    function getRoleAccessUpdate(){

        $where = [];

        $where[] = ['xqgl_id','=',session('shop.xqgl_id')];

        $louyu = Db::name('louyu')->field('louyu_id,louyu_name')
            ->where($where)->whereNull('louyu_pid')->select(); // 楼宇信息数组

        $louyu_ids = Db::name('louyu')
            ->where($where)->whereNull('louyu_pid')->column('louyu_id'); // 楼宇 id 数组

        $danyuan = Db::name('louyu')->field('louyu_id,louyu_name,louyu_pid')
            ->whereIn('louyu_pid',$louyu_ids)->select(); // 单元信息数组

        $danyuan_ids = Db::name('louyu')
            ->whereIn('louyu_pid',$louyu_ids)->column('louyu_id'); // 单元 id 数组

        $fcxx = Db::name('fcxx')->field('fcxx_id,fcxx_fjbh,louyu_id')
            ->whereIn('louyu_id',$danyuan_ids)->select(); // 房产信息数组

        $cksf = Db::name('fcxx')->field('fcxx_id,fcxx_fjbh,louyu_id')
            ->whereIn('louyu_id',$louyu_ids)->select(); // 车库商服信息数组

        $nodes = [];

        foreach ($louyu as $louyu_item) { // 楼宇

            $danyuan_children = [];

            foreach ($danyuan as $danyuan_item) { // 单元

                if ($louyu_item['louyu_id'] == $danyuan_item['louyu_pid']) {

                    $fcxx_children = [];

                    foreach ($fcxx as $fcxx_item) {

                        if ($danyuan_item['louyu_id'] == $fcxx_item['louyu_id']) {

                            $fcxx_children[] = [
                                "access"    => $fcxx_item['fcxx_id'],
                                "title"     => $fcxx_item['fcxx_fjbh'],
                            ];
                        }
                    }

                    $danyuan_children[] = [
                        'access'    => 'D'.$danyuan_item['louyu_id'],
                        'title'     => $danyuan_item['louyu_name'],
                        'children'  => $fcxx_children
                    ];
                }
            }

            $cksf_children = [];

            foreach ($cksf as $cksf_item) {
                if ($louyu_item['louyu_id'] == $cksf_item['louyu_id']) {
                    $cksf_children[] = [
                        "access"    => $cksf_item['fcxx_id'],
                        "title"    => $cksf_item['fcxx_fjbh'],
                    ];
                }
            }

            $danyuan_children[] = [
                'access'    => 'C'.$louyu_item['louyu_id'],
                'title'     => '车库商服',
                'children'  => $cksf_children
            ];

            $nodes[] = [
                'access'    => 'L'.$louyu_item['louyu_id'],
                'title'     => $louyu_item['louyu_name'],
                'children'  => $danyuan_children
            ];

        }

        return json(['status'=>200,'menus'=>$nodes]);
    }

    function getRoleAccessAdd(){

        $where = [];

        $where[] = ['xqgl_id','=',session('shop.xqgl_id')];

        $louyu = Db::name('louyu')->field('louyu_id,louyu_name')
            ->where($where)->whereNull('louyu_pid')->select(); // 楼宇 id

        $louyu_ids = Db::name('louyu')
            ->where($where)->whereNull('louyu_pid')->column('louyu_id'); // 楼宇 id 数组

        $danyuan = Db::name('louyu')->field('louyu_id,louyu_name,louyu_pid')
            ->whereIn('louyu_pid',$louyu_ids)->select(); // 单元 id

        $danyuan_ids = Db::name('louyu')
            ->whereIn('louyu_pid',$louyu_ids)->column('louyu_id'); // 单元 id

        $fcxx = Db::name('fcxx')->field('fcxx_id,fcxx_fjbh,louyu_id')
            ->whereIn('louyu_id',$danyuan_ids)->select(); // 房产 id

        $cksf = Db::name('fcxx')->field('fcxx_id,fcxx_fjbh,louyu_id')
            ->whereIn('louyu_id',$louyu_ids)->select(); // 车库商服信息数组

        $nodes = [];

        foreach ($louyu as $louyu_item) {

            $danyuan_children = [];

            foreach ($danyuan as $danyuan_item) {

                if ($louyu_item['louyu_id'] == $danyuan_item['louyu_pid']) {

                    $fcxx_children = [];

                    foreach ($fcxx as $fcxx_item) {

                        if ($danyuan_item['louyu_id'] == $fcxx_item['louyu_id']) {

                            $fcxx_children[] = [
                                "access"    => $fcxx_item['fcxx_id'],
                                "title"     => $fcxx_item['fcxx_fjbh'],
                            ];
                        }
                    }

                    $danyuan_children[] = [
                        'access'    => '',
                        'title'     => $danyuan_item['louyu_name'],
                        'children'  => $fcxx_children
                    ];
                }
            }

            $cksf_children = [];

            foreach ($cksf as $cksf_item) {
                if ($louyu_item['louyu_id'] == $cksf_item['louyu_id']) {
                    $cksf_children[] = [
                        "access"    => $cksf_item['fcxx_id'],
                        "title"    => $cksf_item['fcxx_fjbh'],
                    ];
                }
            }

            $danyuan_children[] = [
                'access'    => '',
                'title'     => '车库商服',
                'children'  => $cksf_children
            ];

            $nodes[] = [
                'access'    => '',
                'title'     => $louyu_item['louyu_name'],
                'children'  => $danyuan_children
            ];

        }

        return json(['status'=>200,'menus'=>$nodes]);
    }
	/*end*/

}

