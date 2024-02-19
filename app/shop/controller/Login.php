<?php
namespace app\shop\controller;
use think\exception\ValidateException;
use think\facade\Db;
use app\shop\model\Role as RoleModel;

class Login extends Admin{
	//用户登录 
    public function index(){
		if(!$this->request->isPost()) {
			return view('index');
		}else{
			$postField = 'username,password,verify,key';
			$data = $this->request->only(explode(',',$postField),'post',null);
			$this->validate($data,\app\shop\validate\Login::class);
			if($this->checkLogin($data)){
				return json(['status'=>200]);
			}
		}
    }

    /*start*/

    //验证登录
    private function checkLogin($data){	
		$where['account'] = trim($data['username']);
		$where['password']  = md5(trim($data['password']).config('my.password_secrect'));
		$info = Db::name('shop_admin')->where($where)->find();
		if(!$info) {

			throw new ValidateException("请检查用户名或者密码");

		}
		//////////权限判断/////////////////////
		if ($info['root']==1){
			$quanxian = Db::name('shop')->alias('a')->join('shoprole b', 'a.goumai = b.role_id')->field('a.*,b.access')->where('a.shop_id',$info['shop_id'])->find();
		}
		if ($info['root']==0){
			$quanxian = Db::name('shop_admin')->alias('a')->join('gwgl b', 'a.gwgl_id = b.gwgl_id')->field('a.*,b.gwgl_role as access')->where('a.shop_admin_id',$info['shop_admin_id'])->find();
		}
		///////////权限判断//////////////////

		///////////////////////
		/*原版		if(!$info['role_id']) $info['role_id'] = 1;	
		$info['access'] = [];
		*/
		$info['role_id'] = $info['root']; //新加
		//$info['username'] = $info['cname'];//新加
		if(!$info['username']) $info['username'] = $info['account'];
		$info['access'] = explode(',',$quanxian['access']);//新加

        $info['shop_name'] = Db::name('shop')
            ->where('shop_id',$info['shop_id'])->value('shop_name');

        $info['shop_tel'] = Db::name('shop')
            ->where('shop_id',$info['shop_id'])->value('shop_tel');

		//////////////////////	

        if ($info['root'] == 1) {

            $xqgl = Db::name('xqgl')

                ->field('xqgl_id,xqgl_name')

                ->where('shop_id',$info['shop_id'])->find();

            $info['xqgl_id'] = $xqgl['xqgl_id'];

            $info['xqgl_name'] = $xqgl['xqgl_name'];

        } else {

            $info['xqgl_id'] = intval(explode(',',$info['xqgl_ids'])[0]);

            $info['xqgl_name'] = Db::name('xqgl')

                ->field('xqgl_id,xqgl_name')

                ->where('xqgl_id',intval(explode(',',$info['xqgl_ids'])[0]))->value('xqgl_name');

        }

		session('shop', $info);

		session('shop_sign', data_auth_sign($info));

		event('LoginLog',$data['username']);	//写入登录日志

		

        return true;

    }

    /*end*/



	//验证码

	public function verify(){

		$data['data'] = captcha();

		$data['verify_status'] = config('my.verify_status',true);	//验证码开关

		$data['status'] = 200;

	    return json($data);

	}

	



	//退出

    public function logout(){

        session('shop', null);

		session('shop_sign', null);

	    return json(['status'=>200]);

    }

	



}

