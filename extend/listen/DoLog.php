<?php



//操作日志写入数据库



namespace listen;

use app\admin\model\Log as LogModel;

use think\facade\Db;


class DoLog{

	

    public function handle($user){
		
		////////////////////2023年1月26日11:02:11自建日志记录方法
		$rizhi = Db::name('rizhi')->where('rz_status',1)->column('rz_fangfa');
		/////////////////////////

        if(in_array(request()->action(),['add','update','delete']) || in_array(request()->action(),$rizhi)){

			$content = request()->except(['s', '_pjax']);

			if ($content) {

				foreach ($content as $k => $v) {

					if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {

						unset($content[$k]);

					}

				}

			}

		

			$data['application_name'] = app('http')->getName();

			$data['username'] = $user;

			$data['url'] = request()->url(true);

			$data['ip'] = request()->ip();

			$data['useragent'] = request()->server('HTTP_USER_AGENT');

			$data['content'] = json_encode($content,JSON_UNESCAPED_UNICODE);

			$data['create_time'] = time();

			$data['type'] = 2;
			
			$data['shop_id'] = session('shop.shop_id');

			

			LogModel::insert($data);

		}



    }

}