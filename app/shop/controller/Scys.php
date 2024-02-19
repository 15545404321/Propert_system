<?php 
/*
 module:		应收管理控制器
 create_time:	2023-01-10 09:13:59
 author:		
 contact:		
*/

namespace app\shop\controller;
use think\exception\ValidateException;
use app\shop\model\Scys as ScysModel;
use think\facade\Db;

class Scys extends Admin {


	/*
 	* @Description  验证数据权限
 	*/
	function initialize(){
		parent::initialize();
		if(in_array($this->request->action(),['update','getUpdateInfo','delete'])){
			$idx = $this->request->post('scys_id','','serach_in');
			if($idx){
				foreach(explode(',',$idx) as $v){
					$info = ScysModel::find($v);
					if($info['xqgl_id'] <> session('shop.xqgl_id')){
						throw new ValidateException('你没有操作权限');
					}
				}
			}
		}
	}


	/*start*/
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
			$where['scys_id'] = $this->request->post('scys_id', '', 'serach_in');

			$where['scys.shop_id'] = session('shop.shop_id');

			$where['scys.xqgl_id'] = session('shop.xqgl_id');
			$where['scys.scys_ksyf'] = $this->request->post('scys_ksyf', '', 'serach_in');
			$where['scys.scys_jsyf'] = $this->request->post('scys_jsyf', '', 'serach_in');
			$where['scys.jflx_id'] = $this->request->post('jflx_id', '', 'serach_in');
			$where['scys.louyu_id'] = $this->request->post('louyu_id', '', 'serach_in');
			$where['scys.fydy_id'] = ['find in set',$this->request->post('fydy_id', '', 'serach_in')];

			$field = 'scys_id,scys_ksyf,scys_jsyf,louyu_id,fydy_id';

			$withJoin = [
//				'fydy'=>explode(',','fydy_name'),
				'louyu'=>explode(',','louyu_id,louyu_pid,louyu_name'),
				'jflx'=>explode(',','jflx_name'),
			];

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'scys_id desc';

			$query = ScysModel::field($field);

			$res = $query->where(formatWhere($where))->withJoin($withJoin,'left')->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('jflx_id,louyu_id,fydy_id');

			if($ret = hook('hook/Scys@afterShopIndex',$data)){
				return $ret;
			}

			return json($data);
		}
	}
	/*end*/

	/*
 	* @Description  修改排序开关
 	*/
	function updateExt(){
		$postField = 'scys_id,';
		$data = $this->request->only(explode(',',$postField),'post',null);
		if(!$data['scys_id']) throw new ValidateException ('参数错误');
		ScysModel::update($data);
		return json(['status'=>200,'msg'=>'操作成功']);
	}

	/*start*/
	/*
 	* @Description  生成应收
 	*/
	public function add(){
//		$postField = 'shop_id,xqgl_id,scys_ksyf,scys_jsyf,jflx_id,louyu_id,fydy_id';
		$postField = 'shop_id,xqgl_id,scys_ksyf,scys_jsyf,scys_kstime,scys_zztime,jflx_id,louyu_id,fydy_id,scys_scfs,scys_sclx,';
		$data = $this->request->only(explode(',',$postField),'post',null);

//		$this->validate($data,\app\shop\validate\Scys::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['fydy_id'] = implode(',',$data['fydy_id']);

        $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($data['scys_ksyf']) : '';
//        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime($data['scys_jsyf'])+86400 : '';
        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime(date('Y-m-d', strtotime($data['scys_jsyf']."-01 +1 month -1 day"))) : '';

        $data['scys_kstime'] = !empty($data['scys_kstime']) ? strtotime($data['scys_kstime']) : '';
        $data['scys_zztime'] = !empty($data['scys_zztime']) ? strtotime($data['scys_zztime']) : '';
        $data['scys_scgc'] = 1;

        if ($data['scys_scfs'] == 2) {
            $data['scys_ksyf'] = $data['scys_kstime'];
            $data['scys_jsyf'] = $data['scys_zztime'];
        }

        if($ret = hook('hook/Scys@beforShopAdd',$data)){
            return $ret;
        }

		try{
			$res = ScysModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Scys@afterShopAdd',array_merge($data,['scys_id'=>$res]))){
			return $ret;
		}

		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}

    public function checkboxAdd(){
//		$postField = 'shop_id,xqgl_id,scys_ksyf,scys_jsyf,jflx_id,louyu_id,fydy_id';
        $postField = 'shop_id,xqgl_id,scys_ksyf,scys_jsyf,scys_kstime,scys_zztime,jflx_id,louyu_id,fydy_id,scys_scfs,scys_sclx,fcxx_idx';
        $data = $this->request->only(explode(',',$postField),'post',null);
        $data['fcxx_idx'] = implode(',',array_filter($data['fcxx_idx']));

//		$this->validate($data,\app\shop\validate\Scys::class);

        $data['shop_id'] = session('shop.shop_id');
        $data['xqgl_id'] = session('shop.xqgl_id');
        $data['fydy_id'] = implode(',',$data['fydy_id']);

        $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($data['scys_ksyf']) : '';
//        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime($data['scys_jsyf'])+86400 : '';
        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime(date('Y-m-d', strtotime($data['scys_jsyf']."-01 +1 month -1 day"))) : '';

        $data['scys_kstime'] = !empty($data['scys_kstime']) ? strtotime($data['scys_kstime']) : '';
        $data['scys_zztime'] = !empty($data['scys_zztime']) ? strtotime($data['scys_zztime']) : '';
        $data['scys_scgc'] = 1;

        if ($data['scys_scfs'] == 2) {
            $data['scys_ksyf'] = $data['scys_kstime'];
            $data['scys_jsyf'] = $data['scys_zztime'];
        }

        if($ret = hook('hook/Scys@beforShopCheckboxAdd',$data)){
            return $ret;
        }

        $fcxx_idx = explode(',',$data['fcxx_idx']);

        $louyu_id_column = Db::name('fcxx')->where('fcxx_id','in',$fcxx_idx)->column('louyu_id');

        $danyuan_id = Db::name('louyu')->where('louyu_id','in',$louyu_id_column)
            ->whereNotNull('louyu_pid')->column('louyu_pid');

        $louyu_id = Db::name('louyu')->where('louyu_id','in',$louyu_id_column)
            ->whereNull('louyu_pid')->column('louyu_id');

        $data['louyu_id'] = array_unique(array_merge($danyuan_id,$louyu_id))[0];

        try{
            $res = ScysModel::insertGetId($data);
        }catch(\Exception $e){
            throw new ValidateException($e->getMessage());
        }

        if($ret = hook('hook/Scys@afterShopCheckboxAdd',array_merge($data,['scys_id'=>$res]))){
            return $ret;
        }

        return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
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

	/*
 	* @Description  重新生成
 	*/
	public function update(){
//		$postField = 'scys_id,shop_id,xqgl_id,scys_ksyf,scys_jsyf,jflx_id,fydy_id';
		$postField = 'scys_id,shop_id,xqgl_id,scys_ksyf,scys_jsyf,jflx_id,fydy_id,scys_kstime,scys_zztime,scys_scfs,scys_sclx';
		$data = $this->request->only(explode(',',$postField),'post',null);

//		$this->validate($data,\app\shop\validate\Scys::class);

		$data['shop_id'] = session('shop.shop_id');
		$data['xqgl_id'] = session('shop.xqgl_id');
		$data['fydy_id'] = implode(',',$data['fydy_id']);

        $data['scys_ksyf'] = !empty($data['scys_ksyf']) ? strtotime($data['scys_ksyf']) : '';
//        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? strtotime($data['scys_jsyf'])+86400 : '';
        $data['scys_jsyf'] = !empty($data['scys_jsyf']) ? date('Y-m-d', strtotime($data['scys_jsyf']."-01 +1 month -1 day")) : '';

        $data['scys_kstime'] = !empty($data['scys_kstime']) ? strtotime($data['scys_kstime']) : '';
        $data['scys_zztime'] = !empty($data['scys_zztime']) ? strtotime($data['scys_zztime']) : '';
        $data['scys_scgc'] = 1;

        if ($data['scys_scfs'] == 2) {
            $data['scys_ksyf'] = $data['scys_kstime'];
            $data['scys_jsyf'] = $data['scys_zztime'];
        }

		if($ret = hook('hook/Scys@beforShopUpdate',$data)){
			return $ret;
		}

		try{
			ScysModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}

		if($ret = hook('hook/Scys@afterShopUpdate',$data)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'修改成功']);
	}

	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('scys_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'scys_id,shop_id,xqgl_id,scys_ksyf,scys_jsyf,jflx_id,fydy_id,scys_scfs,scys_sclx';
		$res = ScysModel::field($field)->find($id);
		$res['fydy_id'] = explode(',',$res['fydy_id']);

		if ($res['scys_scfs'] == 2) {
            $res['scys_kstime'] = $res['scys_ksyf'];
            $res['scys_zztime'] = $res['scys_jsyf'];
        }
        $res['scys_ksyf'] = date('Y-m',$res['scys_ksyf']);
        $res['scys_jsyf'] = date('Y-m',$res['scys_jsyf']);

		return json(['status'=>200,'data'=>$res]);
	}
/*end*/

	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('scys_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');

		if($ret = hook('hook/Scys@beforShopDelete',$idx)){
			return $ret;
		}

		ScysModel::destroy(['scys_id'=>explode(',',$idx)],true);

		if($ret = hook('hook/Scys@afterShopDelete',$idx)){
			return $ret;
		}

		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('jflx_id,louyu_id,fydy_id')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('jflx_id',explode(',',$list))){
			$data['jflx_ids'] = $this->query("select jflx_id,jflx_name from cd_jflx",'mysql');
		}
		if(in_array('louyu_id',explode(',',$list))){
			$data['louyu_ids'] = $this->query("select louyu_id,louyu_name from cd_louyu where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and louyu_pid is null",'mysql');
		}
		if(in_array('fydy_id',explode(',',$list))){
			$data['fydy_ids'] = $this->query("select fydy_id,fydy_name from cd_fydy where xqgl_id=".session("shop.xqgl_id")." and shop_id=".session("shop.shop_id")." and fylx_id=1",'mysql');
		}
		return $data;
	}



}

