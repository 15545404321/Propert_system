<?php 
/*
 module:		微信菜单控制器
 create_time:	2023-02-14 09:55:02
 author:		
 contact:		
*/

namespace app\admin\controller;
use think\exception\ValidateException;
use app\admin\model\ShopWxConfig as ShopWxConfigModel;
use think\facade\Db;

class ShopWxConfig extends Admin {


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
			$where['shop_wx_config_id'] = $this->request->post('shop_wx_config_id', '', 'serach_in');
			$where['shop_id'] = $this->request->post('shop_id', '', 'serach_in');
			$where['title'] = $this->request->post('title', '', 'serach_in');
			$where['sort'] = $this->request->post('sort', '', 'serach_in');
			$where['type'] = $this->request->post('type', '', 'serach_in');
			$where['url'] = $this->request->post('url', '', 'serach_in');
			$where['xcx_appid'] = $this->request->post('xcx_appid', '', 'serach_in');
			$where['xcx_url'] = $this->request->post('xcx_url', '', 'serach_in');
			$where['cont_code'] = $this->request->post('cont_code', '', 'serach_in');
			$where['media_id'] = $this->request->post('media_id', '', 'serach_in');

			$field = 'shop_wx_config_id,title,sort,type,url,xcx_appid,xcx_url,cont_code,media_id,pid';

			$order  = $this->request->post('order', '', 'serach_in');	//排序字段
			$sort  = $this->request->post('sort', '', 'serach_in');		//排序方式

			$orderby = ($sort && $order) ? $sort.' '.$order : 'shop_wx_config_id asc';

			$query = ShopWxConfigModel::field($field);

			if($this->request->post('onlyTrashed')){
				$query = $query->onlyTrashed();
			}

			$res =$query->where(formatWhere($where))->order($orderby)->paginate(['list_rows'=>$limit,'page'=>$page])->toArray();

			$res['data'] = _generateListTree($res['data'],0,['shop_wx_config_id','pid']);

			$data['status'] = 200;
			$data['data'] = $res;
			$page == 1 && $data['sql_field_data'] = $this->getSqlField('pid');
			return json($data);
		}
	}

	/*
 	* @Description  修改信息之前查询信息的 勿要删除
 	*/
	function getUpdateInfo(){
		$id =  $this->request->post('shop_wx_config_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_wx_config_id,shop_id,pid,title,sort,type,url,xcx_appid,xcx_url,cont_code,media_id';
		$res = ShopWxConfigModel::field($field)->find($id);
		return json(['status'=>200,'data'=>$res]);
	}


	/*
 	* @Description  删除
 	*/
	function delete(){
		$idx =  $this->request->post('shop_wx_config_id', '', 'serach_in');
		if(!$idx) throw new ValidateException ('参数错误');
		ShopWxConfigModel::destroy(['shop_wx_config_id'=>explode(',',$idx)],true);
		return json(['status'=>200,'msg'=>'操作成功']);
	}


	/*
 	* @Description  查看详情
 	*/
	function detail(){
		$id =  $this->request->post('shop_wx_config_id', '', 'serach_in');
		if(!$id) throw new ValidateException ('参数错误');
		$field = 'shop_wx_config_id,title,sort,type,url,xcx_appid,xcx_url,cont_code,media_id';
		$res = ShopWxConfigModel::field($field)->findOrEmpty($id);

		if($res->isEmpty()){
			throw new ValidateException ('信息不存在');
		}

		return json(['status'=>200,'data'=>$res]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	function getFieldList(){
		return json(['status'=>200,'data'=>$this->getSqlField('pid')]);
	}

	/*
 	* @Description  获取定义sql语句的字段信息
 	*/
	private function getSqlField($list){
		$data = [];
		if(in_array('pid',explode(',',$list))){
			$data['pids'] = _generateSelectTree($this->query("select shop_wx_config_id,title,pid from cd_shop_wx_config",'mysql'));
		}
		return $data;
	}


/*start*/
	/*
 	* @Description  添加
 	*/
	public function add(){
		$postField = 'shop_id,pid,title,sort,type,url,xcx_appid,xcx_url,cont_code,media_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopWxConfig::class);

        if(!isset($data['pid'])){
            $data['pid'] = 0;
        }
        
		try{
			$res = ShopWxConfigModel::insertGetId($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'data'=>$res,'msg'=>'添加成功']);
	}


	/*
 	* @Description  修改
 	*/
	public function update(){
		$postField = 'shop_wx_config_id,shop_id,pid,title,sort,type,url,xcx_appid,xcx_url,cont_code,media_id';
		$data = $this->request->only(explode(',',$postField),'post',null);

		$this->validate($data,\app\admin\validate\ShopWxConfig::class);


		if(!isset($data['pid'])){
			$data['pid'] = 0;
		}

		try{
			ShopWxConfigModel::update($data);
		}catch(\Exception $e){
			throw new ValidateException($e->getMessage());
		}
		return json(['status'=>200,'msg'=>'修改成功']);
	}
    /*end*/


/*start*/
    public function submitWxcaidan(){

        $shop_id_val =  $this->request->post('shop_id', '', 'serach_in');
        $shop_id_val =  1;

        $access_token = $this->http_curl_wx($shop_id_val);

        $access_token = json_decode($access_token,true);

        $Wxzdy = new \app\Wxzdy($access_token['access_token'],$shop_id_val);
        $res = $Wxzdy->action();
        if ($res) {
            return json(['status'=>200,'msg'=>'操作成功']);
        }
        return json(['status'=>201,'msg'=>'提交失败：'.$res]);
    }

    public function http_curl_wx($shop_id){

        $wxpz = Db::name('wxpz')->where('shop_id',$shop_id)->find();

        $curl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$wxpz['app_id'].'&secret='.$wxpz['secret'];
        $https=true;
        $method='GET';
        $data = null;
        /*$data = [
            'grant_type' => 'client_credential',
            'appid' => $wxpz['app_id'],
            'secret' => $wxpz['secret']
        ];*/
        // 创建一个新cURL资源
        $ch = curl_init();

        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $curl);  //要访问的网站
        //启用时会将头文件的信息作为数据流输出。
        curl_setopt($ch, CURLOPT_HEADER, false);
        //将curl_exec()获取的信息以字符串返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($https){
            //FALSE 禁止 cURL 验证对等证书（peer's certificate）。
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //验证主机
        }

        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true); //发送 POST 请求
            //全部数据使用HTTP协议中的 "POST" 操作来发送。
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        // 抓取URL并把它传递给浏览器
        $content = curl_exec($ch);

        //关闭cURL资源，并且释放系统资源
        curl_close($ch);

        return $content;
    }
    /*end*/



}

